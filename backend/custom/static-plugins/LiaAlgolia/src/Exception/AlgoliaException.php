<?php declare(strict_types=1);

namespace Lia\Algolia\Exception;

use Shopware\Core\Framework\HttpException;
use Symfony\Component\HttpFoundation\Response;

class AlgoliaException extends HttpException
{
    public const DEFINITION_NOT_FOUND = 'ALGOLIA__DEFINITION_NOT_FOUND';
    public const EMPTY_QUERY = 'ALGOLIA__EMPTY_QUERY';
    public const UNSUPPORTED_FILTER = 'ALGOLIA__UNSUPPORTED_FILTER';
    public const NESTED_AGGREGATION_MISSING = 'ELASTICSEARCH__NESTED_FILTER_AGGREGATION_MISSING';
    public const UNSUPPORTED_AGGREGATION = 'ELASTICSEARCH__UNSUPPORTED_AGGREGATION';

    public static function definitionNotFound(string $definition): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::DEFINITION_NOT_FOUND,
            'Definition {{ definition }} not found',
            ['definition' => $definition]
        );
    }
    public static function emptyQuery(): self
    {
        return new self(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::EMPTY_QUERY,
            'Empty query provided'
        );
    }

    public static function unsupportedFilter(string $filterClass): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::UNSUPPORTED_FILTER,
            'Provided filter of class {{ filterClass }} is not supported',
            ['filterClass' => $filterClass]
        );
    }

    public static function nestedAggregationMissingInFilterAggregation(string $aggregation): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::NESTED_AGGREGATION_MISSING,
            'Filter aggregation {{ aggregation }} contains no nested aggregation.',
            ['aggregation' => $aggregation]
        );
    }

    public static function unsupportedAggregation(string $aggregationClass): self
    {
        return new self(
            Response::HTTP_BAD_REQUEST,
            self::UNSUPPORTED_AGGREGATION,
            'Provided aggregation of class {{ aggregationClass }} is not supported',
            ['aggregationClass' => $aggregationClass]
        );
    }
}