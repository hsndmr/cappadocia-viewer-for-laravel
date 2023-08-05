<?php

declare(strict_types=1);

use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\CappadociaViewer;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\CappadociaViewerClient;

it('sends a viewer message with specified badge, type, and context, then resets data', function (): void {
    // Arrange
    $viewer = $this->spy(CappadociaViewer::class)->makePartial();

    /** @var CappadociaViewer $viewer */
    $viewer = $viewer->setMessage('test message')
        ->setBadge('test badge')
        ->setBadgeType(BadgeType::SUCCESS)
        ->setType(ViewerType::QUERY);

    // Act & Assert
    $this->mock(CappadociaViewerClient::class)
        ->shouldReceive('send')
        ->withArgs(function ($args) {
            return $args['type'] === 'query' &&
                $args['message'] === 'test message' &&
                $args['badge'] === 'test badge' &&
                $args['badgeType'] === 'success' &&
                $args['context'] === [
                    'data' => ['test'],
                ];
        })
        ->once();

    $viewer->send([
        'data' => ['test'],
    ]);

    /* @var \Mockery\LegacyMockInterface $viewer */
    $viewer->shouldHaveReceived('resetData')->once();
});

it('sends a viewer message with an empty context', function (): void {
    // Arrange
    $viewer = new CappadociaViewer();

    // Act & Assert
    $this->mock(CappadociaViewerClient::class)
        ->shouldReceive('send')
        ->withArgs(function ($args) {
            return $args['context'] === '';
        })
        ->once();

    $viewer->send();
});

it('resets the viewer data to default values', function (): void {
    // Arrange
    $viewer = new CappadociaViewer();
    $viewer->setType(ViewerType::QUERY)
        ->setBadge('test badge')
        ->setBadgeType(BadgeType::SUCCESS)
        ->setMessage('test message');

    // Act
    $resetDataReflection = new ReflectionMethod($viewer, 'resetData');
    $resetDataReflection->invoke($viewer);

    // Assert
    $typeProperty = new ReflectionProperty($viewer, 'type');
    expect($typeProperty->getValue($viewer))->toBe(ViewerType::LOG);

    $badgeTypeProperty = new ReflectionProperty($viewer, 'badgeType');
    expect($badgeTypeProperty->getValue($viewer))->toBeNull();

    $badgeProperty = new ReflectionProperty($viewer, 'badge');
    expect($badgeProperty->getValue($viewer))->toBeNull();

    $messageProperty = new ReflectionProperty($viewer, 'message');
    expect($messageProperty->getValue($viewer))->toBe('');
});
