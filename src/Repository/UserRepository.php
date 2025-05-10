<?php

namespace App\Repository;

use App\Doctrine\CommonRepositoryUtilityTrait;
use App\Entity\UserEntity;
use App\Exception\System\BadFilterDataException;
use App\Exception\System\BadOrderParameterException;
use App\Filter\UserListFilter;
use App\Order\OrderApplier;
use App\Pagination\PaginatedResult;
use App\Pagination\PaginationFactory;
use App\Pagination\PaginationTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    use PaginationTrait;
    use CommonRepositoryUtilityTrait;

    public const ALIAS = 'user';
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEntity::class);
    }


    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof UserEntity) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }
        $user->setPassword($newHashedPassword);
        $this->add($user, true);
    }


    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->findOneBy(['email' => $identifier]);
        }
        return null;
    }

    /**
     * @throws BadOrderParameterException
     * @throws BadFilterDataException
     */
    public function findAllFilteredAndPaginated(
        PaginationFactory $paginationFactory,
        UserListFilter $userListFilter,
        OrderApplier $orderApplier
    ): PaginatedResult {
        $qb = $this->createQueryBuilder(self::ALIAS);
        $qb = $userListFilter->apply($qb, self::ALIAS);
        $orderApplier->apply($qb);
        return $this->paginate($qb, $paginationFactory);
    }
}
