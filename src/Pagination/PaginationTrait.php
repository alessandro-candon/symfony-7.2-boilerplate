<?php

namespace App\Pagination;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

use function Symfony\Component\String\u;

trait PaginationTrait
{
    public function paginate(
        QueryBuilder $query,
        PaginationFactory $paginationFactory,
        bool $fetchJoinCollection = true,
    ): PaginatedResult {
        $paginatedResult = new PaginatedResult();
        $paginatedResult->enabled = $paginationFactory->isActive();

        if (! $paginatedResult->enabled) {
            $paginatedResult->data = $query->getQuery()->getResult();

            return $paginatedResult;
        }

        $paginatedResult->startRange = $paginationFactory->getStartRange();
        $paginatedResult->endRange = $paginationFactory->getEndRange();

        $query = $query->setFirstResult($paginationFactory->getStartRange())
            ->setMaxResults($paginationFactory->getEndRange() - $paginationFactory->getStartRange() + 1);
        $paginator = new Paginator($query, $fetchJoinCollection);
        $paginatedResult->totalCount = $paginator->count();

        foreach ($paginator as $item) {
            $paginatedResult->data[] = $item;
        }

        /** @phpstan-ignore function.alreadyNarrowedType */
        $paginatedResult->acceptedRanges = method_exists($this, 'getClassName')
            ? u($this->getClassName())->afterLast('\\')->lower()->toString()
            : null;

        return $paginatedResult;
    }
}
