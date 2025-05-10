<?php

declare(strict_types=1);

namespace App\Pagination;

use App\Exception\System\PaginationException;

use function implode;
use function in_array;

class PaginatedResult
{
    public const LINK_SELF     = 'self';
    public const LINK_FIRST    = 'first';
    public const LINK_PREVIOUS = 'previous';
    public const LINK_NEXT     = 'next';
    public const LINK_LAST     = 'last';

    public array $data = [];

    public int $startRange = 0;

    public int $endRange = 20;

    public string|null $acceptedRanges = '';

    public int $totalCount = 0;

    public array $link;

    public bool $enabled = true;

    public function __construct()
    {
        $this->link = [
            self::LINK_SELF     => '',
            self::LINK_NEXT     => '',
            self::LINK_PREVIOUS => '',
            self::LINK_FIRST    => '',
            self::LINK_LAST     => '',
        ];
    }

    /**
     * @throws PaginationException
     */
    public function setPartialLink(string $key, string $url): static
    {
        if (
            ! in_array($key, [
                self::LINK_SELF,
                self::LINK_NEXT,
                self::LINK_PREVIOUS,
                self::LINK_FIRST,
                self::LINK_LAST,
            ])
        ) {
            throw new PaginationException('Invalid Link Key: ' . $key);
        }

        $this->link[$key] = $url;

        return $this;
    }

    public function getParsedLink(): string
    {
        $parsedLinks = [];
        foreach ($this->link as $key => $link) {
            $parsedLinks[] = $link . ';rel="' . $key . '"';
        }
        return implode(', ', $parsedLinks);
    }
}
