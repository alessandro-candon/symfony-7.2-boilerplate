<?php

namespace App\Service\UserService;

use App\DTO\Request\User\UserCreateRequestDto;
use App\DTO\Request\User\UserUpdateRequestDto;
use App\Entity\UserEntity;
use App\Exception\System\BadFilterDataException;
use App\Exception\System\BadOrderParameterException;
use App\Filter\UserListFilter;
use App\Order\OrderApplier;
use App\Pagination\PaginationFactory;
use App\Repository\UserRepository;
use App\Security\RolesEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Pagination\PaginatedResult;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createUser(UserCreateRequestDto $userCreateRequest): UserEntity
    {
        $user = new UserEntity(
            $userCreateRequest->getUsername(),
            $userCreateRequest->getFirstName(),
            $userCreateRequest->getLastName(),
            $userCreateRequest->getEmail(),
            'not-real-password'
        );
        $passwordHashed = $this->passwordHasher->hashPassword($user, $userCreateRequest->getPassword());
        $user->setPassword($passwordHashed);
        $this->userRepository->add($user, true);
        return $user;
    }

    /**
     * @throws BadOrderParameterException
     * @throws BadFilterDataException
     */
    public function listUsers(
        PaginationFactory $paginationFactory,
        UserListFilter $userListFilter,
        OrderApplier $orderApplier
    ): PaginatedResult {
        return $this->userRepository->findAllFilteredAndPaginated($paginationFactory, $userListFilter, $orderApplier);
    }

    public function putUser(int|UserEntity $userEntity, UserUpdateRequestDto $updateRequestDto): UserEntity
    {
        if (!empty(array_diff($updateRequestDto->getRoles(), RolesEnum::getRoles()))) {
            throw new \InvalidArgumentException('Invalid roles');
        }
        if (is_int($userEntity)) {
            $userEntity = $this->userRepository->find($userEntity);
        }

        $userEntity->setUsername($updateRequestDto->getUsername());
        $userEntity->setFirstName($updateRequestDto->getFirstName());
        $userEntity->setLastName($updateRequestDto->getLastName());
        $userEntity->setEmail($updateRequestDto->getEmail());
        $userEntity->setRoles($updateRequestDto->getRoles());

        $this->entityManager->flush();
        return $userEntity;
    }

    public function deleteUser(int|UserEntity $userEntity): void
    {
        if (is_int($userEntity)) {
            $userEntity = $this->userRepository->find($userEntity);
        }
        $this->userRepository->remove($userEntity, true);
    }
}
