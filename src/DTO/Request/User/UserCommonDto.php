<?php

namespace App\DTO\Request\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserCommonDto
{
    public function __construct(
        #[
            Assert\NotBlank,
            Assert\Length(max: 255)
        ]
        private readonly string $username,
        #[
            Assert\NotBlank,
            Assert\Length(max: 255)
        ]
        private readonly string $firstName,
        #[
            Assert\NotBlank,
            Assert\Length(max: 255)
        ]
        private readonly string $lastName,
        #[
            Assert\NotBlank,
            Assert\Email,
            Assert\Length(max: 255)
        ]
        private readonly string $email
    ) {
    }


    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}
