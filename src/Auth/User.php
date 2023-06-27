<?php

declare(strict_types=1);

namespace App\Auth;

use Psr\Http\Message\ServerRequestInterface;

class User
{

    public const USER_KEY = '__user__';

    public static function fromRequest(ServerRequestInterface $request): ?UserInterface
    {
        return $request->getAttribute(self::USER_KEY, null);
    }

}