<?php

declare(strict_types=1);

namespace App\Auth;

interface UserPasswordInterface extends UserInterface
{

    public function getPlainPassword(): ?string;

    public function setPassword(string $password): UserPasswordInterface;

}