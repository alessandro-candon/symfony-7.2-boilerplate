<?php

namespace App\DTO\Request\User;

use JMS\Serializer\Annotation\Type;

class UserUpdateRequestDto extends UserCommonDto
{
    #[Type("array<string>")]
    private array $roles;
    private bool $canReceiveNotifications;

    public function __construct(
        string $username,
        string $firstName,
        string $lastName,
        string $email,
        array $roles,
        bool $canReceiveNotifications
    ) {
        parent::__construct($username, $firstName, $lastName, $email);
        $this->roles = $roles;
        $this->canReceiveNotifications = $canReceiveNotifications;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getCanReceiveNotifications(): bool
    {
        return $this->canReceiveNotifications;
    }
}
