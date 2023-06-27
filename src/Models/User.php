<?php

declare(strict_types=1);

namespace App\Models;

use App\Auth\UserPasswordInterface;
use App\Auth\UserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class User implements UserInterface, UserPasswordInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string")]
    private string $username;

    #[ORM\Column(type: "string")]
    private string $password;

    private ?string $plainPassword;

    /**
     * @var string[]
     */
    #[ORM\Column(type: "json")]
    private array $roles = [];

    private bool $isAuthenticated = false;

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @inheritDoc
     */
    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    /**
     * @inheritDoc
     */
    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $newPassword
     * @return $this
     */
    public function setPassword(string $newPassword): User
    {
        $this->plainPassword = $newPassword;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     * @return $this
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @param bool $authenticated
     * @return $this
     */
    public function setAuthenticated(bool $authenticated = true): User
    {
        $this->isAuthenticated = $authenticated;
        return $this;
    }
}