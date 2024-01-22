<?php
namespace App\Enums\Common;

use App\Enums\Enum;

final class UPLOAD_TYPE extends Enum
{
    const DEFAULT = 'default';
    const DIARY = 'diary';

    protected static $labelMaps = [
        self::DEFAULT => '默认',
        self::DIARY => '记事本',
    ];
}
