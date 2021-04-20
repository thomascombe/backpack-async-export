<?php

namespace Thomascombe\BackpackAsyncExport;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Thomascombe\BackpackAsyncExport\Commands\BackpackAsyncExportCommand;

class BackpackAsyncExportServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('backpack_async_export')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_backpack_async_export_table')
            ->hasRoutes('backpack/export')
            ->hasTranslations()
            ->hasCommand(BackpackAsyncExportCommand::class);
    }
}
