<p align="center"><img src="/docs/images/banner.png" alt="Social Card of Laravel Backpack Async Export"></p>

# Laravel Backpack Async Export

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thomascombe/backpack-async-export.svg?style=flat-square)](https://packagist.org/packages/thomascombe/backpack-async-export)
[![PHPCS check](https://github.com/thomascombe/backpack-async-export/actions/workflows/phpcs.yml/badge.svg)](https://github.com/thomascombe/backpack-async-export/actions/workflows/phpcs.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/thomascombe/backpack-async-export.svg?style=flat-square)](https://packagist.org/packages/thomascombe/backpack-async-export)

This is a package to manage async export in Backpack for Laravel

<p align="center"><img src="/docs/images/demo.png" alt="Demo of Laravel Backpack Async Export"></p>

## Installation

You can install the package via composer:

```bash
composer require thomascombe/backpack-async-export
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Thomascombe\BackpackAsyncExport\BackpackAsyncExportServiceProvider" --tag="backpack-async-export-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Thomascombe\BackpackAsyncExport\BackpackAsyncExportServiceProvider" --tag="backpack-async-export-config"
```

This is the contents of the published config file:

```php
return [
    'user_model' => 'App\Models\User',
    'export_model' => \Thomascombe\BackpackAsyncExport\Models\Export::class,
    'admin_route' => 'export',
    'export_memory_limit' => '2048M',
    'disk' => 'local',
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

public function getExport(): Export
{
    return Export::create([
        Export::COLUMN_USER_ID => backpack_user()->id,
        Export::COLUMN_STATUS => ExportStatus::Created,
        Export::COLUMN_FILENAME => sprintf('export/users_%s.xlsx', now()->toIso8601String()),
        Export::COLUMN_EXPORT_TYPE => UserExport::class,
    ]);
}

public function getExportParameters(): array
{
    return [];
}
```

## Need more?

### Override Export model
You can override `Export` model using config : `export_model`.  
Your model class **need** to implement `\Thomascombe\BackpackAsyncExport\Models\Export`.  

```php
class Export extends \Thomascombe\BackpackAsyncExport\Models\Export
{
}
```

### Update export name
Package allow to change export name with interface on your export class: `Thomascombe\BackpackAsyncExport\Exports\ExportWithName` 

```php

namespace App\Exports;

use Thomascombe\BackpackAsyncExport\Exports\ExportWithName;

class UserExport implements ExportWithName
{
    public static function getName(): string
    {
        return 'My export name';
    }
}
```

<p align="center"><img src="/docs/images/demo_name.png" style="max-width:500px" alt="Demo with custom name of Laravel Backpack Async Export"></p>

### Multi export by CRUD?

You can easily have multi export on save CRUD.  
Your CRUD controller need to implement `Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\MultiExportableCrud` interface.  

```php
public function getAvailableExports(): array
{
    return [
        'default' => null,
        'all' => 'All',
    ];
}
```
**Array keys**: key for query params and dynamic method name  
**Array values**: Export name (display in CRUD button)  

For each new export you have to add news methods: 
```php
public function getExport*All*(): Export
{
    return Export::create(...);
}

public function getExport*All*Parameters(): array
{
    return [...];
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
