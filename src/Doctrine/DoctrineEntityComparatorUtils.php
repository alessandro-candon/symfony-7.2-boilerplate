<?php

namespace App\Doctrine;

use App\Utils\ArrayUtils;
use Doctrine\ORM\PersistentCollection;

final class DoctrineEntityComparatorUtils
{
    /**
     * @param string[] $fields
     * @param array<string, array{mixed, mixed}|PersistentCollection> $entityChangeSet
     */
    public static function hasChangeOnFields(array $fields, array $entityChangeSet): bool
    {
        return ArrayUtils::any($entityChangeSet, static function ($changeSetValue, $changeSetKey) use ($fields) {
            return
                in_array($changeSetKey, $fields) &&
                DoctrineEntityComparatorUtils::compareEntityDecimal($changeSetValue);
        });
    }

    /**
     * Why we do this? check here => https://www.php.net/manual/en/language.types.float.php
     *
     * @param array<int, mixed> $changeSetValues
     */
    public static function compareEntityDecimal(array $changeSetValues): bool
    {
        if (gettype($changeSetValues[0]) === 'double' || gettype($changeSetValues[1]) === 'double') {
            return abs($changeSetValues[0] - $changeSetValues[1]) > 1E-5;
        }

        return true;
    }
}
