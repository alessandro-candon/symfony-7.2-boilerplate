<?php

namespace App\Service\UserService;

use App\DTO\Request\User\UserCreateRequestDto;
use App\DTO\Request\User\UserUpdateRequestDto;
use App\Entity\UserEntity;
use App\Filter\UserListFilter;
use App\Order\OrderApplier;
use App\Pagination\PaginatedResult;
use App\Pagination\PaginationFactory;

interface UserServiceInterface
{
    public function createUser(UserCreateRequestDto $userCreateRequest): UserEntity;

    public function listUsers(
        PaginationFactory $paginationFactory,
        UserListFilter $userListFilter,
        OrderApplier $orderApplier
    ): PaginatedResult;

    public function putUser(int|UserEntity $userEntity, UserUpdateRequestDto $updateRequestDto): UserEntity;

    public function deleteUser(int|UserEntity $userEntity): void;
}
