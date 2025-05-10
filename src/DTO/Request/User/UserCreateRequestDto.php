<?php

namespace App\DTO\Request\User;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class UserCreateRequestDto extends UserCommonDto
{
    #[
        Assert\NotBlank,
        Assert\Length(min: 5, max: 255),
        Assert\PasswordStrength([
            'minScore' => PasswordStrength::STRENGTH_MEDIUM
        ])
    ]
    private string $password;

    public function __construct(string $username, string $firstName, string $lastName, string $email, string $password)
    {
        parent::__construct($username, $firstName, $lastName, $email);
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
