<?php

namespace App\Filter;

use Doctrine\ORM\QueryBuilder;

class UserListFilter extends FilterApplier
{
    public const AVAILABLE_FILTERS = [
        'full_text_search' => 'fullTextSearchFilter',
        'email' => 'emailFilter',
    ];

    public function fullTextSearchFilter(
        string $fullTextSearch,
        QueryBuilder $queryBuilder,
        string $alias
    ): QueryBuilder {
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like($alias . '.email', ':fullTextSearch'),
                $queryBuilder->expr()->like($alias . '.firstName', ':fullTextSearch'),
                $queryBuilder->expr()->like($alias . '.lastName', ':fullTextSearch'),
            )
        );
        $queryBuilder->setParameter('fullTextSearch', '%' . $fullTextSearch . '%');
        return $queryBuilder;
    }

    public function emailFilter(string $email, QueryBuilder $queryBuilder, string $alias): QueryBuilder
    {
        $queryBuilder->andWhere($queryBuilder->expr()->eq($alias . '.email', ':email'));
        $queryBuilder->setParameter('email', $email);
        return $queryBuilder;
    }
}
