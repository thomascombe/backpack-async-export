# Laravel Backpack Async Export

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thomascombe/backpack_async_export.svg?style=flat-square)](https://packagist.org/packages/thomascombe/backpack_async_export)
[![PHPCS check](https://github.com/thomascombe/backpack-async-export/actions/workflows/phpcs.yml/badge.svg)](https://github.com/thomascombe/backpack-async-export/actions/workflows/phpcs.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/thomascombe/backpack_async_export.svg?style=flat-square)](https://packagist.org/packages/thomascombe/backpack_async_export)

This is a package to manage async export in Backpack for Laravel

## Installation

You can install the package via composer:

```bash
composer require thomascombe/backpack_async_export
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Thomascombe\BackpackAsyncExport\BackpackAsyncExportServiceProvider" --tag="backpack_async_export-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Thomascombe\BackpackAsyncExport\BackpackAsyncExportServiceProvider" --tag="backpack_async_export-config"
```

This is the contents of the published config file:

```php
return [
    'user_model' => 'App\Models\User',
    'admin_route' => 'export',
    'export_memory_limit' => '2048M',
];
```

## Usage

### Add export item in menu
```bash
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('export') }}'><i class='nav-icon la la-file-export'></i> <span>Export</span></a></li>"
```

### Create you export class
```bash
php artisan make:export UserExport --model=App\Models\User
```
For all details, have a look at [Laravel Excel Package](https://laravel-excel.com/)

### Create your controller
```bash
php artisan backpack:crud {Name}CrudController
```

### Your controller need to implement interface
```php
use Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\ExportableCrud;

class {Name}CrudController extends CrudController implements ExportableCrud {}
```

### Use awesome trait
```php
use Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Traits\HasExportButton;
```

### Call method to add buttons
```php
public function setup()
{
    // ...
    $this->addExportButtons();
}
```

### Add method to your CRUD controller
```php
use Thomascombe\BackpackAsyncExport\Enums\ExportStatus;
use Thomascombe\BackpackAsyncExport\Models\Export;

public function getExport(): array
{
    $export = Export::create([
        Export::COLUMN_USER_ID => backpack_user()->id,
        Export::COLUMN_STATUS => ExportStatus::Created,
        Export::COLUMN_FILENAME => sprintf('export/users_%s.xlsx', now()->toIso8601String()),
        Export::COLUMN_EXPORT_TYPE => MyExportClass::class,
    ]);
    return [
        $export,
        [
            // Export class parameters
        ],
    ];
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [thomascombe](https://github.com/thomascombe)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
