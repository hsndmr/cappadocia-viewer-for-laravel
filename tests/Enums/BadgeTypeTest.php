<?php

declare(strict_types=1);

use Hsndmr\CappadociaViewer\Enums\BadgeType;

it('returns the correct type for each case', function (): void {
    expect(BadgeType::PRIMARY->type())->toBe('primary')
        ->and(BadgeType::SUCCESS->type())->toBe('success')
        ->and(BadgeType::INFO->type())->toBe('info')
        ->and(BadgeType::WARNING->type())->toBe('warning')
        ->and(BadgeType::ERROR->type())->toBe('error');
});

it('converts log levels to the correct badge types', function (): void {
    expect(BadgeType::fromLogLevel('debug'))->toBe(BadgeType::INFO)
        ->and(BadgeType::fromLogLevel('notice'))->toBe(BadgeType::INFO)
        ->and(BadgeType::fromLogLevel('info'))->toBe(BadgeType::INFO)
        ->and(BadgeType::fromLogLevel('warning'))->toBe(BadgeType::WARNING)
        ->and(BadgeType::fromLogLevel('error'))->toBe(BadgeType::ERROR)
        ->and(BadgeType::fromLogLevel('critical'))->toBe(BadgeType::ERROR)
        ->and(BadgeType::fromLogLevel('alert'))->toBe(BadgeType::ERROR)
        ->and(BadgeType::fromLogLevel('emergency'))->toBe(BadgeType::ERROR)
        ->and(BadgeType::fromLogLevel('other'))->toBe(BadgeType::PRIMARY);
});
