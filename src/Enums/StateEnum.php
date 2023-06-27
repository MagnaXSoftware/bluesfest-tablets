<?php

declare(strict_types=1);

namespace App\Enums;

use Elao\Enum\ReadableEnum;

/**
 * @extends ReadableEnum<string>
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
    public const TRAILER = "trailer";
    public const OTHER = "other";

    public static function values(): array
    {
        return [
            self::NA,
            self::STORED,
            self::DEPLOYED,
            self::TRAILER,
            self::OTHER,
        ];
    }

    public static function readables(): array
    {
        return [
            self::NA => "N/A",
            self::STORED => "Stored in Box",
            self::DEPLOYED => "Deployed",
            self::TRAILER => "In IT Trailer",
            self::OTHER => "Other",
        ];
    }

}