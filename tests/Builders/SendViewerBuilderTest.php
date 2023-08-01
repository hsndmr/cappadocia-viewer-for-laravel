<?php

declare(strict_types=1);

use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;
use Hsndmr\CappadociaViewer\Builders\SenderViewerBuilder;
use Hsndmr\CappadociaViewer\DataTransferObjects\ViewerDto;

it('can build and send a viewer', function (): void {
    // Arrange
    $viewerType = ViewerType::LOG;
    $badgeType  = BadgeType::INFO;
    $message    = 'Test Message';
    $badge      = 'Test Badge';
    $context    = ['key' => 'value'];

    $viewerDto = new ViewerDto(
        type: $viewerType,
        message: $message,
        badge: $badge,
        badgeType: $badgeType,
        context: $context
    );

    // Act & Assert
    CappadociaViewer::shouldReceive('sendViewer')->once()->withArgs(function (ViewerDto $argument) use ($viewerDto) {
        return $argument->type === $viewerDto->type &&
            $argument->message === $viewerDto->message &&
            $argument->badge === $viewerDto->badge &&
            $argument->badgeType === $viewerDto->badgeType &&
            $argument->context === $viewerDto->context;
    });

    (new SenderViewerBuilder($message))
        ->setType($viewerType)
        ->setBadgeType($badgeType)
        ->setBadge($badge)
        ->send($context);
});
