<?php

declare(strict_types=1);

namespace App\Tests\Common;

use App\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

use function assert;

abstract class EnhancedWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $manager;

    protected Container $container;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = static::getContainer();
        $manager = $this->container->get(EntityManagerInterface::class);
        assert($manager instanceof EntityManager);
        $this->manager = $manager;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->manager->close();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    protected function persistModel(mixed $model): void
    {
        $this->manager->persist($model);
        $this->manager->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    protected function callAsLogged(array $roles = []): UserEntity
    {
        $faker = Factory::create();
        $user = new UserEntity(
            $faker->userName() . '_test',
            $faker->firstName() . '_test',
            $faker->lastName() . '_test',
            $faker->firstName() . '_test@' . $faker->domainName(),
            $faker->password()
        );
        $user->setRoles($roles);
        $this->persistModel($user);
        $this->client->loginUser($user);
        return $user;
    }
}
