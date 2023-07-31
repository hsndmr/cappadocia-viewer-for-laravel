<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Log;
use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;

it('handles log event without exceptions', function (): void {
    // Arrange & Assert
    CappadociaViewer::shouldReceive('sendViewer')
        ->once()
        ->withArgs(function ($arg) {
            return $arg->type === ViewerType::LOG &&
                $arg->message === 'test message' &&
                $arg->badge === 'info' &&
                $arg->badgeType === BadgeType::fromLogLevel('info');
        });

    // Act
    Log::info('test message');
});

it('handles log event with not empty context', function (): void {
    // Arrange
    $context = ['key' => 'value'];

    //  Assert
    CappadociaViewer::shouldReceive('sendViewer')
        ->once()
        ->withArgs(function ($arg) use ($context) {
            return $arg->context === $context;
        });

    // Act
    Log::info('test message', $context);
});

it('handles log event with empty context', function (): void {
    // Arrange && Assert
    CappadociaViewer::shouldReceive('sendViewer')
        ->once()
        ->withArgs(function ($arg) {
            return $arg->context === null;
        });

    // Act
    Log::info('test message');
});

it('does not handle log event with exception in context', function (): void {
    // Arrange & Assert
    CappadociaViewer::shouldReceive('sendViewer')->never();

    // Act
    Log::info('test message', ['exception' => new Exception('test exception')]);
});
