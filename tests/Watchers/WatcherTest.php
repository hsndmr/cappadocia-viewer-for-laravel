<?php

declare(strict_types=1);

use Hsndmr\CappadociaViewer\Watchers\JobWatcher;

it('starts and stops watching correctly', function (): void {
    // Arrange
    $watcher = new JobWatcher();

    // Act & Assert
    $watcher->watch();
    expect($watcher->isWatching())->toBeTrue();

    $watcher->stopWatching();
    expect($watcher->isWatching())->toBeFalse();
});

it('watches when config is enabled', function (): void {
    // Arrange
    config()->set('cappadocia-viewer.watch_jobs', true);

    $watcher = new JobWatcher();

    // Act & Assert
    expect($watcher->isWatching())->toBeTrue();

    config()->set('cappadocia-viewer.watch_jobs', false);
});
