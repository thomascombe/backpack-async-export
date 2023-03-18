<p align="center"><img src="/docs/images/banner.png" alt="Social Card of Laravel Backpack Async Export"></p>

# Laravel Backpack Async Export

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thomascombe/backpack-async-export.svg?style=flat-square)](https://packagist.org/packages/thomascombe/backpack-async-export)
[![PHPCS check](https://github.com/thomascombe/backpack-async-export/actions/workflows/phpcs.yml/badge.svg)](https://github.com/thomascombe/backpack-async-export/actions/workflows/phpcs.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/thomascombe/backpack-async-export.svg?style=flat-square)](https://packagist.org/packages/thomascombe/backpack-async-export)

This is a package to manage async export and import in [Backpack](https://backpackforlaravel.com/) for Laravel

<p align="center"><img src="/docs/images/demo.png" alt="Demo of Laravel Backpack Async Export"></p>
<p align="center"><img src="/docs/images/demo_ended.png" alt="Demo of Laravel Backpack Async Export"></p>

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
    'feature_enabled' => [
        'export' => true,
        'import' => true,
    ],
    'user_model' => 'App\Models\User',
    'import_export_model' => Thomascombe\BackpackAsyncExport\Models\ImportExport::class,
    'admin_export_route' => 'export',
    'admin_import_route' => 'import',
    'export_memory_limit' => '2048M',
    'disk' => 'local',
];
```

## Usage for export

### Add export item in menu
```bash
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('export') }}'><i class='nav-icon la la-file-export'></i> <span>Export</span></a></li>"
```

### Create you export class
```bash
php artisan make:export UserExport --model=App/Models/User
```
For all details, have a look at [Laravel Excel Package](https://laravel-excel.com/).

You can make your export class extends our [LaravelExcel](./src/Exports/LaravelExcel.php) abstract.

### Create your controller
```bash
php artisan backpack:crud {ModelName}
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
use Thomascombe\BackpackAsyncExport\Enums\ActionType;
use Thomascombe\BackpackAsyncExport\Enums\ImportExportStatus;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;

public function getExport(): ImportExport
{
    return ImportExport::create([
        ImportExport::COLUMN_USER_ID => backpack_user()->id,
        ImportExport::COLUMN_ACTION_TYPE => ActionType::Export,
        ImportExport::COLUMN_STATUS => ImportExportStatus::Created,
        ImportExport::COLUMN_FILENAME => sprintf('export/users_%s.xlsx', now()->toIso8601String()),
        ImportExport::COLUMN_EXPORT_TYPE => UserExport::class,
    ]);
}

public function getExportParameters(): array
{
    return [];
}
```

### Simple csv export

It may sometimes be necessary to export large amounts of data. PhpSpreadsheet (used behind the hood by this package)
does not always offer the best performance. In this case, it is recommended to use the low-level functions of PHP, such
as [fputcsv](https://www.php.net/manual/function.fputcsv.php).

This package has an abstract class [SimpleCsv](./src/Exports/SimpleCsv.php) which can be extended to use this export
mode. Of course, it is more limited and only allows you to define a query, headers and a mapping between the model and
the data table to be exported.

```php
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Thomascombe\BackpackAsyncExport\Exports\SimpleCsv;

class UserExport extends SimpleCsv
{
    public function query(): EloquentBuilder
    {
        return User::query();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
        ];
    }
}

```

## Usage for import

### Add import item in menu
```bash
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('import') }}'><i class='nav-icon la la-file-import'></i> <span>Import</span></a></li>"
```

### Create you import class
```bash
php artisan make:import UserImport --model=App/Models/User
```
For all details, have a look at [Laravel Excel Package](https://laravel-excel.com/)

### Create your controller
```bash
php artisan backpack:crud {Name}CrudController
```

### Your controller need to implement interface
```php
use Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Interfaces\ImportableCrud;

class {Name}CrudController extends CrudController implements ImportableCrud {}
```

### Use awesome trait
```php
use Thomascombe\BackpackAsyncExport\Http\Controllers\Admin\Traits\HasImportButton;
```

### Call method to add buttons
```php
public function setup()
{
    // ...
    $this->addImportButtons();
}
```

### Add method to your CRUD controller

```php
use Thomascombe\BackpackAsyncExport\Enums\ActionType;
use Thomascombe\BackpackAsyncExport\Enums\ImportExportStatus;
use Thomascombe\BackpackAsyncExport\Models\ImportExport;

public function getImport(): ImportExport
{
    return ImportExport::create([
        ImportExport::COLUMN_USER_ID => backpack_user()->id,
        ImportExport::COLUMN_ACTION_TYPE => ActionType::Import->value,
        ImportExport::COLUMN_STATUS => ImportExportStatus::Created,
        ImportExport::COLUMN_FILENAME => '',
        ImportExport::COLUMN_EXPORT_TYPE => UserImport::class,
    ]);
}

public function getImportParameters(): array
{
    return [
        'private' => [
            'hint' => 'CSV file required',
            'mimetypes' => ['text/csv', 'application/csv'],
        ],
    ];
}
```

## Need more?

### Override ImportExport model
You can override `ImportExport` model using config : `import_export_model`.  
Your model class **need** to implement `\Thomascombe\BackpackAsyncExport\Models\ImportExport`.

```php
class ImportExport extends \Thomascombe\BackpackAsyncExport\Models\ImportExport
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
public function getExport*All*(): ImportExport
{
    return ImportExport::create(...);
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
