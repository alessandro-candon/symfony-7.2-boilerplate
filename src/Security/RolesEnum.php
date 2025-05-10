<?php

namespace App\Security;

/**
 * Is mandatory to replicate this logic also inside the security.yaml file
 */
enum RolesEnum
{
    case ROLE_USER;
    case IS_AUTHENTICATED;

    public static function getRoles(): array
    {
        return [
            self::ROLE_USER->name,
            self::IS_AUTHENTICATED->name,
        ];
    }
}
