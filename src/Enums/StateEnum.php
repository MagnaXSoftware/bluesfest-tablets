<?php

namespace App\Enums;

use Elao\Enum\ReadableEnum;

/**
 * @method static StateEnum NA()
 * @method static StateEnum STORED()
 * @method static StateEnum DEPLOYED()
 * @method static StateEnum OTHER()
 */
final class StateEnum extends ReadableEnum
{
    public const NA = "na";
    public const STORED = "stored";
    public const DEPLOYED = "deployed";
    public const OTHER = "other";

    public static function values(): array
    {
        return [
            self::NA,
            self::STORED,
            self::DEPLOYED,
            self::OTHER,
        ];
    }

    public static function readables(): array
    {
        return [
            self::NA => "N/A",
            self::STORED => "Closed",
            self::DEPLOYED => "Open",
            self::OTHER => "Other",
        ];
    }

}