<?php

declare(strict_types=1);

namespace App\Pagination;

use Symfony\Component\HttpFoundation\Request;

use function filter_var;
use function intval;
use function Symfony\Component\String\u;

use const FILTER_VALIDATE_BOOLEAN;

class PaginationFactory
{
    public const PAGINATION_PARAMS = 'paginate';
    public const START_RANGE = 0;
    public const END_RANGE = 20;

    private function __construct(
        private readonly int $startRange,
        private readonly int $endRange,
        private readonly bool $isActive = false,
    ) {
    }

    public static function createFromRequest(Request $request): self
    {
        $isActive = filter_var(
            $request->query->get(self::PAGINATION_PARAMS, true),
            FILTER_VALIDATE_BOOLEAN,
        );

        $range = (string) $request->query->get('range', '');

        $rangeString = u($range);

        if ($rangeString->match('/^[0-9]+-[0-9]+$/')) {
            return new self(
                intval($rangeString->before('-')->toString()),
                intval($rangeString->after('-')->toString()),
                $isActive,
            );
        }

        return new self(self::START_RANGE, self::END_RANGE, $isActive);
    }

    public function getStartRange(): int
    {
        return $this->startRange;
    }

    public function getEndRange(): int
    {
        return $this->endRange;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
