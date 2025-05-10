<?php

namespace App\Controller\v1\User;

use App\Annotation\View;
use App\Controller\DtoControllerTrait;
use App\DTO\Request\User\UserCreateRequestDto;
use App\DTO\Request\User\UserUpdateRequestDto;
use App\Entity\UserEntity;
use App\Exception\System\InvalidPayloadException;
use App\Filter\UserListFilter;
use App\Order\OrderApplier;
use App\Pagination\PaginatedResult;
use App\Pagination\PaginationFactory;
use App\Security\RolesEnum;
use App\Service\UserService\UserServiceInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/v1/users', name: 'app_v1_user_')]
class UserController extends AbstractController
{
    use DtoControllerTrait;

    /**
     * @throws InvalidPayloadException
     */
    #[IsGranted(RolesEnum::ROLE_ADMIN->name, message: 'You must be an admin to access this route')]
    #[Route('', name: 'post', methods: ['POST'])]
    #[View(statusCode: Response::HTTP_OK, groups: ['show_id', 'user_index'])]
    public function post(Request $request, UserServiceInterface $userService): UserEntity
    {
        $payload = $this->deserializeAndValidate((string) $request->getContent(), UserCreateRequestDto::class);
        assert($payload instanceof  UserCreateRequestDto);
        return $userService->createUser($payload);
    }

    #[IsGranted(RolesEnum::ROLE_ADMIN->name, message: 'You must be an admin to access this route')]
    #[Route('', name: 'list', methods: ['GET'])]
    #[View(statusCode: Response::HTTP_OK, groups: ['show_id', 'user_index'])]
    public function list(
        Request $request,
        UserServiceInterface $userService
    ): PaginatedResult {
        $userListFilter = new UserListFilter($request->query->all());
        $userListOrder = new OrderApplier(['user.id', 'user.email'], $request->query->all());
        $paginationFactory = PaginationFactory::createFromRequest($request);
        return $userService->listUsers($paginationFactory, $userListFilter, $userListOrder);
    }

    #[IsGranted(RolesEnum::IS_AUTHENTICATED->name, message: 'You must be logged to access this route')]
    #[Route('/me', name: 'me', methods: ['GET'])]
    #[View(statusCode: Response::HTTP_OK, groups: ['show_id', 'user_index', 'timestampable'])]
    public function me(): UserEntity
    {
        $user = $this->getUser();
        assert($user instanceof UserEntity);
        return $user;
    }

    /**
     * @throws InvalidPayloadException
     */
    #[IsGranted(RolesEnum::ROLE_ADMIN->name, message: 'You must be an admin to access this route')]
    #[Route('/{id}', name: 'put', methods: ['PUT'])]
    #[View(statusCode: Response::HTTP_OK, groups: ['show_id', 'user_index', 'timestampable'])]
    public function put(
        #[MapEntity(id: 'id')] UserEntity $user,
        Request $request,
        UserServiceInterface $userService
    ) {
        $payload = $this->deserializeAndValidate((string) $request->getContent(), UserUpdateRequestDto::class);
        assert($payload instanceof  UserUpdateRequestDto);
        return $userService->putUser($user, $payload);
    }

    #[IsGranted(RolesEnum::ROLE_ADMIN->name, message: 'You must be an admin to access this route')]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[View(statusCode: Response::HTTP_NO_CONTENT)]
    public function delete(
        #[MapEntity(id: 'id')] UserEntity $user,
        UserServiceInterface $userService
    ): void {
        $userService->deleteUser($user);
    }
}
