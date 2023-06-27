<?php

declare(strict_types=1);

namespace App\Auth\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
class HasRole
{
    /**
     * @var string[]
     */
    private array $perms;

    /**
     * @param string|string[] $perms
     */
    public function __construct(array|string $perms)
    {
        if (is_string($perms)) {
            $perms = explode(',', $perms);
        }

        $this->perms = $perms;
    }

}