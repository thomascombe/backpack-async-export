# Laravel Backpack Async Export

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thomascombe/backpack_async_export.svg?style=flat-square)](https://packagist.org/packages/thomascombe/backpack_async_export)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/thomascombe/backpack_async_export/run-tests?label=tests)](https://github.com/thomascombe/backpack_async_export/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/thomascombe/backpack_async_export/Check%20&%20fix%20styling?label=code%20style)](https://github.com/thomascombe/backpack_async_export/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
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
];
```

## Usage

```php
$backpack_async_export = new Thomascombe\BackpackAsyncExport();
echo $backpack_async_export->echoPhrase('Hello, Spatie!');
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
