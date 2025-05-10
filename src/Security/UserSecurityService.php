<?php

namespace App\Security;

use App\Repository\UserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserSecurityService implements UserRepositoryInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function getUserEntityByUserCredentials(
        string $username,
        string $password,
        string $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        $user = $this->userRepository->findOneBy(['email' => $username]);
        if ($user && $this->passwordHasher->isPasswordValid($user, $password)) {
            return $user;
        }
        return null;
    }
}
