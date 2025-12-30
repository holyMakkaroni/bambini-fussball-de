<?php declare(strict_types=1);

namespace Lia\SeoSuite\Framework\Routing;

use Lia\SeoSuite\LiaSeoSuite;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Routing\RequestTransformerInterface;
use Shopware\Core\SalesChannelRequest;
use Shopware\Storefront\Framework\Routing\RequestTransformer;
use Symfony\Component\HttpFoundation\Request;

class FilteredRequestTransformer implements RequestTransformerInterface
{
    private RequestTransformerInterface $decorated;

    private EntityRepository $propertyGroupOptionRepository;

    protected EntityRepository $manufacturerRepository;

    public function __construct(
        RequestTransformerInterface $decorated,
        EntityRepository $propertyGroupOptionRepository,
        EntityRepository $manufacturerRepository
    ) {
        $this->decorated = $decorated;
        $this->propertyGroupOptionRepository = $propertyGroupOptionRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * Transform the already transformed request by Shopware to further process the filters we added to the pathInfo
     * in the FilterSeoResolver. The /f/filter1/option/filter2/option2 string needs to be parsed to
     * query parameters as if they were passed in the form of ?properties=propertyId|property2Id
     *
     * @param Request $request
     *
     * @return Request
     */
    public function transform(Request $request): Request
    {
        $request = $this->decorated->transform($request);
        $queries = $request->query;

        $pathInfo = $request->getPathInfo();
        if (preg_match('/[^\/]+--[^\/]+.*$/', $request->getPathInfo(), $filters)) {
            # Remove the filter string that we added in the FilterSeoResolver from the path info to avoid a
            # route not found exception later on.
            $pathInfo = preg_replace('/[^\/]+--[^\/]+.*$/', '', $request->getPathInfo());
        }

        # No filters found in the URL?
        if (empty($filters)) {
            return $request;
        }

        $filterQueries = [];
        foreach($queries as $key => $value) {
            if(str_contains($key, LiaSeoSuite::PARAM_PREFIX . '-')) {
                $key = str_replace(LiaSeoSuite::PARAM_PREFIX . '-', '', $key);
                $filterQueries[$key] = key_exists($key, $filterQueries) ? [...$filterQueries[$key], $value] : $value;
            }
        }

        # Convert the SEO filter string to the native Shopware property id query parameters, so they can be handled by Shopware
        $transformedQueryVars = array_merge(
            $request->query->all(),
            $this->constructQueryFromSeoFilterString($filters[0], $filterQueries, Context::createDefaultContext())
        );

        # Fix the request URI with the stripped path info to avoid a 404 issue
        $transformedServerVars = array_merge(
            $request->server->all(),
            ['REQUEST_URI' => rtrim($request->getBaseUrl(), '/') . $pathInfo]
        );

        $originalAttributes = $request->attributes->all();
        $attributes = [
            'resolved-uri' => $pathInfo,
        ];

        if ($request->attributes->has(SalesChannelRequest::ATTRIBUTE_CANONICAL_LINK)) {
            # Append the filters to the SEO canonical URL to ensure correct redirects
            $filterCanonical =
                rtrim($request->attributes->get(SalesChannelRequest::ATTRIBUTE_CANONICAL_LINK), '/')
                . '/f/' . $filters[1];

            $redirectUrl = $request->server->get('REDIRECT_URL') ?: "";

            # If canonical URL does not contain the REDIRECT_URL it means we can safely set the new canonical URL
            # which will be used for the redirect.
            if (
                $redirectUrl
                && !str_contains($filterCanonical, $redirectUrl)
                && !$request->server->get('REDIRECT_STATUS')
            ) {
                $attributes[SalesChannelRequest::ATTRIBUTE_CANONICAL_LINK] = $filterCanonical;
            } else {
                # To avoid a redirect loop, we need to remove the canonical link from the attributes
                unset($originalAttributes[SalesChannelRequest::ATTRIBUTE_CANONICAL_LINK]);
            }
        }

        $canonicalPath = '';
        if ($request->attributes->has(RequestTransformer::ORIGINAL_REQUEST_URI)) {
            $parsedUrl = parse_url(
                $request->attributes->get(RequestTransformer::ORIGINAL_REQUEST_URI)
            );

            $canonicalPath = !empty($parsedUrl['path']) ? $parsedUrl['path'] : '';
        }

        $attributes['zeobv-faceted-navigation-filter-canonical'] =
            $request->attributes->get(RequestTransformer::SALES_CHANNEL_ABSOLUTE_BASE_URL) . $canonicalPath;

        # Fix the resolved URI with the stripped path info to avoid a 404 issue
        $transformedAttributesVars = array_merge(
            $originalAttributes,
            $attributes
        );

        $originalRequest = $request;

        # Create a new request with our updated values
        $request = $request->duplicate(
            $transformedQueryVars,
            $request->request->all(),
            $transformedAttributesVars,
            $request->cookies->all(),
            $request->files->all(),
            $transformedServerVars
        );

        # Reapply headers from the original request which got strips during the duplication process
        foreach ($originalRequest->headers->all() as $key => $value) {
            $request->headers->set($key, $value);
        }

        return $request;
    }

    public function extractInheritableAttributes(Request $sourceRequest): array
    {
        return $this->decorated->extractInheritableAttributes(...func_get_args());
    }

    /**
     * Parse a SEO friendly filter string (e.g. /group/option/group/option1/group2/option/)
     * to a string of propertyGroupOption ID's (e.g. dc6f98beeca44852beb078a9e8e21e7|7C78c53f3f6dd14eb4927978415bfb74db)
     * for use in the "properties" key in the query object of the request for Shopware to parse during
     * CMS page load
     *
     *
     * @param string  $filterString
     * @param Context $context
     *
     * @return array
     */
    private function constructQueryFromSeoFilterString(string $filterString, array $filterQueries, Context $context): array
    {
        # Convert a string (e.g. /group/option/group/option1/group2/option/) to a mapped array
        # format of array: {group: [option, option1], group2: [option]}
        $optionsMappedByGroup = $this->processFilters($filterString, $filterQueries);

        $query = [];

        if (key_exists('manufacturer', $optionsMappedByGroup)) {
            if ($manuIds = $this->getManufacturerIdsFromUrlKeys($optionsMappedByGroup['manufacturer'], $context)) {
                $query['manufacturer'] = implode('|', $manuIds);
            }

            unset($optionsMappedByGroup['manufacturer']);
        }

        if ($propsIds = $this->getPropertyGroupOptionIdsFromGroupOptionMap($optionsMappedByGroup, $context)) {
            $query['properties'] = implode('|', $propsIds);
        }

        return $query;
    }

    /**
     * Retrieve propertyGroupOption ID's from the options in the provided GroupOptionMap
     *
     * @param array   $groupOptionMap
     * @param Context $context
     *
     * @return array
     */
    private function getPropertyGroupOptionIdsFromGroupOptionMap(array $groupOptionMap, Context $context): array
    {
        $criteria = new Criteria();

        $multiFilter = [];
        foreach ($groupOptionMap as $translatedGroupFilterName => $translatedOptionValues) {
            foreach ($translatedOptionValues as $translatedOptionFilterValue) {
                $multiFilter[] = new MultiFilter(MultiFilter::CONNECTION_AND, [
                    new ContainsFilter(
                        'group.customFields.filterName',
                        $translatedGroupFilterName
                    ),
                    new ContainsFilter(
                        'customFields.optionValue',
                        $translatedOptionFilterValue
                    ),
                ]);
            }
        }

        if (empty($multiFilter)) {
            return [];
        }

        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, $multiFilter));

        $result = $this->propertyGroupOptionRepository->searchIds($criteria, $context);

        return $result->getIds();
    }

    /**
     * Retrieve manufacturers ID's from the options in the provided $manufacturerUrlKeys
     *
     * @param array   $manufacturerUrlKeys
     * @param Context $context
     *
     * @return array
     */
    private function getManufacturerIdsFromUrlKeys(array $manufacturerUrlKeys, Context $context): array
    {
        $criteria = new Criteria();

        $multiFilter = [];
        foreach ($manufacturerUrlKeys as $manufacturerFilterName) {
            $multiFilter[] = new MultiFilter(MultiFilter::CONNECTION_OR, [
                new ContainsFilter(
                    'translations.customFields',
                    "\"filterName\":\"$manufacturerFilterName\""
                ),
                new ContainsFilter(
                    'translations.customFields',
                    "\"filterName\": \"$manufacturerFilterName\""
                ),
            ]);
        }

        if (empty($multiFilter)) {
            return [];
        }

        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, $multiFilter));

        $result = $this->manufacturerRepository->searchIds($criteria, $context);

        return $result->getIds();
    }

    /**
     * Map a filter string (e.g. /group/option/group/option1/group2/option/)
     * to an array {group: [option, option1], group2: [option]}
     *
     * @param string $filterString
     *
     * @return array
     */
    private function processFilters(string $filterString, array $filterQueries): array
    {
        $filters = explode('/', $filterString);
        $map = [];
        foreach($filters as $filter) {
            if(empty($filter)) {
                continue;
            }
            [$propertyGroupSlug, $propertyGroupOptionSlug] = explode('--', $filter);

            $map[$propertyGroupSlug] = key_exists($propertyGroupSlug, $map)
                ? [...$map[$propertyGroupSlug], $propertyGroupOptionSlug]
                : [$propertyGroupOptionSlug];
        }

        foreach($filterQueries as $key => $values) {
            foreach($values as $value) {
                $map[$key] = key_exists($key, $map) ? [...$map[$key], $value] : [$value];
            }
        }

        return $map;
    }
}
