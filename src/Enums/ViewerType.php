<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Enums;

enum ViewerType
{
    case LOG;
    case JOB;

    public function type(): string
    {
        return match ($this) {
            self::LOG => 'log',
            self::JOB => 'job',
        };
    }
}
