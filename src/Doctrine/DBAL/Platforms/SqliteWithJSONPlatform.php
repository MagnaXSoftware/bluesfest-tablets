<?php

declare(strict_types=1);

namespace App\Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Platforms\SqlitePlatform;

class SqliteWithJSONPlatform extends SqlitePlatform
{
    public function hasNativeJsonType()
    {
        return true;
    }

    public function getJsonTypeDeclarationSQL(array $column)
    {
        return 'JSON';
    }


}