<?php

namespace App\Utils;

final class ArrayUtils
{
    /** @param array<mixed> $array */
    public static function get(mixed $key, array $array, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (! str_contains($key, '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_array($array) || ! array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * This function returns an array cleaned without key => null values.
     *
     * @param array<string, mixed> $array
     *
     * @return array<string, mixed>
     */
    public static function arrayWithoutNullValues(array $array): array
    {
        return array_filter($array, static fn (mixed $value) => $value !== null);
    }

    /** @param array<mixed> $array */
    public static function any(array $array, callable $fn): bool
    {
        foreach ($array as $key => $value) {
            if ($fn($value, $key)) {
                return true;
            }
        }

        return false;
    }
}
