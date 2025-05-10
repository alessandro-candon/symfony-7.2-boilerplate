<?php

declare(strict_types=1);

namespace App\Annotation;

use Attribute;
use Symfony\Component\HttpFoundation\Response;

#[Attribute]
class View
{
    /** @param array|mixed[] $groups */
    public function __construct(public int $statusCode = Response::HTTP_OK, public array $groups = [])
    {
    }
}
