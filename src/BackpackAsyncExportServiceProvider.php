<?php

namespace Thomascombe\BackpackAsyncExport;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->name('backpack-async-export')
            ->hasConfigFile('backpack-async-import-export')
            ->hasViews()
            ->hasMigrations(['create_backpack_async_import_exports_table'])
            ->hasRoutes('backpack/import-export')
            ->hasTranslations();
    }
}
