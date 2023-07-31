<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer;

use Spatie\LaravelPackageTools\Package;
use Hsndmr\CappadociaViewer\Watchers\Watcher;
use Hsndmr\CappadociaViewer\Watchers\LogWatcher;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CappadociaViewerServiceProvider extends PackageServiceProvider
{
    const WATCHERS = [
        LogWatcher::class,
    ];

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('cappadocia-viewer')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->registerWatchers();
    }

    public function packageBooted(): void
    {
        $this->bootWatchers();
    }

    protected function registerWatchers(): void
    {
        foreach (self::WATCHERS as $watcherClass) {
            $this->app->singleton($watcherClass);
        }
    }

    protected function bootWatchers(): void
    {
        if (!config('cappadocia-viewer.enabled')) {
            return;
        }

        foreach (self::WATCHERS as $watcherClass) {
            /** @var Watcher $watcher */
            $watcher = app($watcherClass);

            $watcher->register();
        }
    }
}
