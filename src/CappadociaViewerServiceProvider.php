<?php

namespace Hsndmr\CappadociaViewer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Hsndmr\CappadociaViewer\Commands\CappadociaViewerCommand;

class CappadociaViewerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('cappadocia-viewer')
            ->hasConfigFile()
            ->hasCommand(CappadociaViewerCommand::class);
    }
}
