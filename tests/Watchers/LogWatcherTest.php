<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Log\Events\MessageLogged;
use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\Watchers\LogWatcher;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;

it('handles log event without exceptions', function (): void {

    // Arrange
    $logWatcher = $this->spy(LogWatcher::class)->makePartial();

    $logWatcher
        ->shouldReceive('shouldHandleLog')
        ->once()
        ->andReturnTrue();

    // Act & Assert
    $eventMock = $this->mock(MessageLogged::class, function (MockInterface $mock): void {
        $mock->message = 'message';
        $mock->level   = 'info';
        $mock->context = [];
    });

    CappadociaViewer::partialMock()
        ->shouldReceive('setMessage')
        ->once()
        ->with('message')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setBadge')
        ->once()
        ->with('info')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setBadgeType')
        ->once()
        ->with(BadgeType::INFO)
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setType')
        ->once()
        ->with(ViewerType::LOG)
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('send')
        ->once()
        ->with([]);

    /* @var LogWatcher $logWatcher */
    $logWatcher->handleLog($eventMock);
});

it('should not send a message if shouldHandleLog method returns false', function (): void {

    // Arrange
    $logWatcher = $this->spy(LogWatcher::class)->makePartial();

    $eventMock = $this->mock(MessageLogged::class);

    $logWatcher
        ->shouldReceive('shouldHandleLog')
        ->with($eventMock)
        ->once()
        ->andReturnTrue();

    // Act & Assert
    CappadociaViewer::partialMock()->shouldNotReceive('setMessage');

    /* @var LogWatcher $logWatcher */
    $logWatcher->handleLog($eventMock);
});

it('registers to listen for MessageLogged events', function (): void {
    // Arrange
    $logWatcher = new LogWatcher();

    // Act & Assert
    Event::partialMock()
        ->shouldReceive('listen')
        ->with(MessageLogged::class, [$logWatcher, 'handleLog']);

    $logWatcher->register();
});

it('returns config name correctly', function (): void {
    $logWatcher = new LogWatcher();

    $getConfigNameReflection = new ReflectionMethod($logWatcher, 'getConfigName');

    expect($getConfigNameReflection->invoke($logWatcher))->toBe('logs');
});
