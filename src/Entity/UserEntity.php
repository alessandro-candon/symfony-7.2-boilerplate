<?php

namespace App\Entity;

use App\Doctrine\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JMS\Serializer\Annotation\Groups;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class UserEntity implements UserInterface, PasswordAuthenticatedUserInterface, UserEntityInterface
{
    use TimestampableTrait;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_id', 'user_index'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user_index', 'ticket_index', 'ticket_list'])]
    private string $username;

    #[ORM\Column(length: 180)]
    #[Groups(['user_index'])]
    private string $firstName;

    #[ORM\Column(length: 180)]
    #[Groups(['user_index'])]
    private string $lastName;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user_index', 'ticket_index', 'ticket_list'])]
    private string $email;

    #[ORM\Column]
    #[Groups(['user_index'])]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups(['user_index'])]
    private bool $canReceiveNotifications;

    public function __construct(
        string $username,
        string $firstName,
        string $lastName,
        string $email,
        string $password
    ) {
        $this->username = $username;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->canReceiveNotifications = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function eraseCredentials(): void
    {
    }


    public function getIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getCanReceiveNotifications(): bool
    {
        return $this->canReceiveNotifications;
    }

    public function setCanReceiveNotifications(bool $canReceiveNotifications): void
    {
        $this->canReceiveNotifications = $canReceiveNotifications;
    }
}
