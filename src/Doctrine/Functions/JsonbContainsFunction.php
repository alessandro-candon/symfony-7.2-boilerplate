<?php

declare(strict_types=1);

namespace App\Doctrine\Functions;

final class JsonbContainsFunction extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('%s @> %s');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
