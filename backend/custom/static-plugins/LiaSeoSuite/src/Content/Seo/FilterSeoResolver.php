<?php declare(strict_types=1);

namespace Lia\SeoSuite\Content\Seo;

use Shopware\Core\Content\Seo\AbstractSeoResolver;

class FilterSeoResolver extends AbstractSeoResolver
{
    protected AbstractSeoResolver $decorated;

    public function __construct(AbstractSeoResolver $decorated)
    {
        $this->decorated = $decorated;
    }

    public function getDecorated(): AbstractSeoResolver
    {
        return $this->decorated;
    }

    public function resolve(string $languageId, string $salesChannelId, string $pathInfo): array
    {
        // If we find filters in the url, we strip them allow the SEO path to be resolved by shopware
        if (preg_match('/[^\/]+--[^\/]+.*$/', $pathInfo, $filters)) {
            $modifiedPathInfo = preg_replace('/[^\/]+--[^\/]+.*$/', '', $pathInfo) . "/";
        } else {
            # No filters found, we can use the original path info
            return $this->decorated->resolve(...func_get_args());
        }

        # Result should contain the resolved "ugly" path info eg. /navigation/{navigationId}
        # with a separate 'id' parameter with which Shopware can resolve the category at a later point
        $result = $this->decorated->resolve($languageId, $salesChannelId, $modifiedPathInfo);

        # If Shopware couldn't resolve the path we try again without a trailing slash
        if (!key_exists('id', $result)) {
            $result = $this->decorated->resolve($languageId, $salesChannelId, rtrim($modifiedPathInfo, '/'));
        }

        # If we found filters in the SEO route earlier we re-add them now Shopware managed to resolve everything.
        if (!empty($filters)) {
            $result['pathInfo'] .= $filters[0];
        }

        # Return the resolve result to be further processed in the FilteredRequestTransformer
        # this is essential to avoid running into a 404 down the line anyway due to the added filters in the pathInfo
        return $result;
    }
}
