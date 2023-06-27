<?php

declare(strict_types=1);

namespace App\Form\Extension\Psr7;

use Symfony\Component\Form\AbstractExtension;

class Psr7Extension extends AbstractExtension
{
    protected function loadTypeExtensions(): array
    {
        return [
            new Type\FormTypePsr7Extension(),
        ];
    }

}