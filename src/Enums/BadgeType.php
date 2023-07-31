<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Enums;

enum BadgeType
{
    case PRIMARY;
    case SUCCESS;
    case INFO;
    case WARNING;
    case ERROR;

    public function type(): string
    {
        return match ($this) {
            self::SUCCESS => 'success',
            self::WARNING => 'warning',
            self::ERROR   => 'error',
            self::INFO    => 'info',
            default       => 'primary',
        };
    }

    public static function fromLogLevel(string $level): BadgeType
    {
        return match ($level) {
            'debug', 'notice', 'info' => self::INFO,
            'warning' => self::WARNING,
            'error', 'critical', 'alert', 'emergency' => self::ERROR,
            default => self::PRIMARY,
        };
    }
}
