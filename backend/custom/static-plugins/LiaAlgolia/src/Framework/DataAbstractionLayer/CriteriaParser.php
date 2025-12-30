<?php declare(strict_types=1);

namespace Lia\Algolia\Framework\DataAbstractionLayer;

use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\PriceField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\Filter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\SuffixFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\XOrFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class CriteriaParser
{
    const NUMERIC_ATTRIBUTES = [
        'visibilities.visibility',
        'defaultPricing.gross'
    ];

    public function __construct(
        private readonly EntityDefinitionQueryHelper $helper,
    ) {
    }

    public function buildAccessor(EntityDefinition $definition, string $fieldName, Context $context): string
    {
        $root = $definition->getEntityName();

        $parts = explode('.', $fieldName);
        if ($root === $parts[0]) {
            array_shift($parts);
        }

        $field = $this->helper->getField($fieldName, $definition, $root, false);
        if ($field instanceof TranslatedField) {
            $ordered = [];
            foreach ($parts as $part) {
                $ordered[] = $part;
            }
            $parts = $ordered;
        }

        if (!$field instanceof PriceField) {
            return implode('.', $parts);
        }

        if (\in_array(end($parts), ['net', 'gross'], true)) {
            $taxState = end($parts);
            array_pop($parts);
        } elseif ($context->getTaxState() === CartPrice::TAX_STATE_GROSS) {
            $taxState = 'gross';
        } else {
            $taxState = 'net';
        }

        $currencyId = $context->getCurrencyId();
        if (Uuid::isValid((string) end($parts))) {
            $currencyId = end($parts);
            array_pop($parts);
        }

        $parts[] = 'c_' . $currencyId;
        $parts[] = $taxState;

        return implode('.', $parts);
    }

    /**
     * Parse the Shopware Criteria object to Algolia search parameters.
     *
     * @param Criteria $criteria
     * @return string
     */
    public function parseCriteria(Criteria $criteria): array
    {
        $algoliaQuery = [];

        // Handle filters
        $filters = $this->parseFilters($criteria->getFilters());
        if (!empty($filters)) {
            $algoliaQuery['filters'] = implode(' AND ', $filters);
        }

        // Handle postFilters
        $postFilters = $this->parseFilters($criteria->getPostFilters());
        if (!empty($postFilters)) {
            $filters = $this->prepareFacetFilters($postFilters);

            foreach ($filters as $filter) {
                $algoliaQuery['facetFilters'][] = $filter;
            }
        }

        return $algoliaQuery;
    }

    /**
     * Parse filters from Shopware Criteria to Algolia filters.
     *
     * @param array $filters
     * @return array
     */
    private function parseFilters(array $filters, string $operator = 'AND', bool $multifilter = false): array
    {
        $parsedFilters = [];

        foreach ($filters as $filter) {
            if ($filter instanceof EqualsFilter) {
                $parsedFilters[] = $this->getFieldName($filter->getField()) . ':' . $this->convertQuery($filter->getValue());
            } elseif ($filter instanceof RangeFilter) {
                $range = $filter->getParameters();
                $field = $this->getFieldName($filter->getField());
                if (isset($range[RangeFilter::GTE])) {
                    $parsedFilters[] = $field . ' >= ' . $range[RangeFilter::GTE];
                }
                if (isset($range[RangeFilter::GT])) {
                    $parsedFilters[] = $field . ' > ' . $range[RangeFilter::GT];
                }
                if (isset($range[RangeFilter::LTE])) {
                    $parsedFilters[] = $field . ' <= ' . $range[RangeFilter::LTE];
                }
                if (isset($range[RangeFilter::LT])) {
                    $parsedFilters[] = $field . ' < ' . $range[RangeFilter::LT];
                }
            } elseif ($filter instanceof ContainsFilter) {
                $parsedFilters[] = $this->getFieldName($filter->getField()) . ':*' . $filter->getValue() . '*';
            } elseif ($filter instanceof PrefixFilter) {
                $parsedFilters[] = $this->getFieldName($filter->getField()) . ':' . $filter->getValue() . '*';
            } elseif ($filter instanceof SuffixFilter) {
                $parsedFilters[] = $this->getFieldName($filter->getField()) . ':*' . $filter->getValue();
            } elseif ($filter instanceof EqualsAnyFilter) {
                $values = array_map(fn($value) => $value, $filter->getValue());
                if(!empty($values)) {
                    if($multifilter) {
                        $parsedFilters['operator'] = $operator;
                        $parsedFilters['queries'][$this->getFieldName($filter->getField())] = array_map(fn($value) => $value, $values);
                    } else {
                        $parsedFilters[] = [
                            'operator' => $operator,
                            'queries' => [
                                $this->getFieldName($filter->getField()) => array_map(fn($value) => $value, $values)
                            ]
                        ];
                    }
                }
            } elseif ($filter instanceof NotFilter) {
                $subFilters = $this->parseFilters($filter->getQueries());
                $parsedFilters[] = 'NOT ' . implode(' AND ', $subFilters);
            } elseif ($filter instanceof MultiFilter) {
                $operator = $this->getOperator($filter);
                $subFilters = $this->parseFilters($filter->getQueries(), $operator, true);

                if($operator === 'AND') {
                    foreach($subFilters as $subFilter) {
                        $parsedFilters[] = $subFilter;
                    }
                } else {
                    $parsedFilters[] = $subFilters;
                }
            }
        }
        return $parsedFilters;
    }

    /**
     * Get the logical operator for a MultiFilter.
     *
     * @param Filter $filter
     * @return string
     */
    private function getOperator(Filter $filter): string
    {
        if ($filter instanceof MultiFilter) {
            return $filter->getOperator() === MultiFilter::CONNECTION_AND ? 'AND' : 'OR';
        } elseif ($filter instanceof AndFilter) {
            return 'AND';
        } elseif ($filter instanceof OrFilter) {
            return 'OR';
        } elseif ($filter instanceof XOrFilter) {
            return 'XOR';
        }
        return ' AND ';
    }

    private function getFieldName(string $fieldName): string
    {
        $parts = explode('.', $fieldName);

        if(count($parts) > 1) {
            array_shift($parts);
            return implode('.', $parts);
        }

        return $fieldName;
    }

    private function convertQuery($query): mixed
    {
        if(is_bool($query)) {
            return $query ? 'true' : 'false';
        } elseif (is_null($query)) {
            return 'null';
        }

        return $query;
    }

    private function prepareFacetFilters(array $filters): array
    {
        $mergedFilters = [];
        $groupedFilters = [];
        $propertyResult = [];
        $manufacturerResult = [];
        $query = [];

        foreach ($filters as $filter) {
            $operator = $filter['operator'];

            if(isset($filter['queries']['optionIds']) && isset($filter['queries']['propertyIds'])) {
                $mergedFilters['propertyIds'][] = array_merge($filter['queries']['optionIds'], $filter['queries']['propertyIds']);
            }

            if(isset($filter['queries']['manufacturerId'])) {
                $mergedFilters['manufacturerId'][] = $filter['queries']['manufacturerId'];
            }
        }

        if(!empty($mergedFilters['propertyIds'])) {
            foreach($mergedFilters['propertyIds'] as $filter) {
                $groupedFilters[] = array_unique($filter);
            }

            $i = 0;
            foreach ($groupedFilters as $filter) {
                foreach($filter as $item) {
                    $propertyResult[$i][] = ['optionIds:' . $item, 'propertyIds:' . $item];
                }

                if($operator === 'OR') {
                    $query[] = array_merge(...array_values($propertyResult[$i]));
                }
                $i++;
            }
        }

        if(isset($mergedFilters['manufacturerId'])) {
            foreach($mergedFilters['manufacturerId'] as $manufacturer) {
                foreach($manufacturer as $item) {
                    $manufacturerResult[] = 'manufacturerId:' . $item;
                }
            }

            $query[] = $manufacturerResult;
        }

        return $query;
    }
}