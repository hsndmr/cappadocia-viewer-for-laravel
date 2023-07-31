<?php

declare(strict_types=1);

use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\DataTransferObjects\ViewerDto;

it('converts the ViewerDto object to an array', function (): void {
    // Arrange
    $type      = ViewerType::LOG;
    $message   = 'Test message';
    $badge     = 'info';
    $badgeType = BadgeType::fromLogLevel('info');
    $context   = ['key' => 'value'];

    $viewerDto = new ViewerDto($type, $message, $badge, $badgeType, $context);

    // Act
    $array = $viewerDto->toArray();

    // Assert
    expect($array)->toMatchArray([
        'type'      => $type->type(),
        'message'   => $message,
        'badge'     => $badge,
        'badgeType' => $badgeType->type(),
        'context'   => $context,
    ])->and($array['timestamp'])->toBeInt();
});
