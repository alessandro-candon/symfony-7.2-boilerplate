<?php

namespace App\Filter;

use App\Exception\System\BadFilterDataException;
use Doctrine\ORM\QueryBuilder;

abstract class FilterApplier
{
    public const DATE_FORMAT = 'Y-m-d';

    public function __construct(
        protected readonly array $rawQueryParameters
    ) {
    }

    public function apply(QueryBuilder $queryBuilder, string $alias): QueryBuilder
    {
        if (!defined('static::AVAILABLE_FILTERS')) {
            throw new BadFilterDataException('Not static::AVAILABLE_FILTERS are defined');
        }
        foreach (static::AVAILABLE_FILTERS as $filterName => $filterMethod) {
            if (array_key_exists($filterName, $this->rawQueryParameters)) {
                $queryBuilder = $this->$filterMethod($this->rawQueryParameters[$filterName], $queryBuilder, $alias);
            }
        }
        return $queryBuilder;
    }
}
