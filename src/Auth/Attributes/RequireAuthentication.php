<?php

declare(strict_types=1);

namespace App\Auth\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
class RequireAuthentication
{
    private bool $required;

    public function __construct(bool $required = true)
    {
        $this->required = $required;
    }

    public function isAuthenticationRequired(): bool
    {
        return $this->required;
    }
}