<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Enums;

enum ViewerType
{
    case LOG;
    case JOB;
    case QUERY;
    case REQUEST;

    public function type(): string
    {
        return match ($this) {
            self::LOG     => 'log',
            self::JOB     => 'job',
            self::QUERY   => 'query',
            self::REQUEST => 'request',
        };
    }
}
