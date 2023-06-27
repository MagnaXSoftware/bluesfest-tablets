<?php

declare(strict_types=1);

namespace App\Auth;

class AnonymousUser implements UserInterface
{

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritDoc
     */
    public function getUserIdentifier(): string
    {
        return '[anonymous]';
    }

    /**
     * @inheritDoc
     */
    public function isAuthenticated(): bool
    {
        return false;
    }
}