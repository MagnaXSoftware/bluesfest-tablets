<?php

namespace App\Doctrine\DBAL\Types;

use App\Enums\StateEnum;
use Elao\Enum\Bridge\Doctrine\DBAL\Types\AbstractEnumType;

/**
 * @extends AbstractEnumType<StateEnum>
 */
class StateEnumType extends AbstractEnumType
{
    public const NAME = 'StateEnum';

    protected function getEnumClass(): string
    {
        return StateEnum::class;
    }

    public function getName(): string
    {
        return static::NAME;
    }
}