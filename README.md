# Laravel package for storing model versions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eribloo/laravel-model-snapshots.svg?style=flat-square)](https://packagist.org/packages/eribloo/laravel-model-snapshots)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/eribloo/laravel-model-snapshots/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/eribloo/laravel-model-snapshots/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/eribloo/laravel-model-snapshots/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/eribloo/laravel-model-snapshots/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eribloo/laravel-model-snapshots.svg?style=flat-square)](https://packagist.org/packages/eribloo/laravel-model-snapshots)

This package allows creating snapshots of models. Snapshots are stored in separate table.

## Installation

You can install the package via composer:

```bash
composer require eribloo/laravel-model-snapshots
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-model-snapshots-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-model-snapshots-config"
```

This is the contents of the published config file:

```php
return [

    /**
     * Snapshot class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\SnapshotInterface interface.
     */
    'snapshot_class' => Snapshot::class,

    /**
     * Versionist class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface interface.
     */
    'versionist_class' => IncrementingVersionist::class,

    /**
     * Determine if hidden attributes should be stored.
     */
    'should_snapshot_hidden' => true,
];
```

## Usage

TBD

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

[//]: # (## Contributing)

[//]: # ()

[//]: # (Please see [CONTRIBUTING]&#40;CONTRIBUTING.md&#41; for details.)

[//]: # (## Security Vulnerabilities)

[//]: # ()

[//]: # (Please review [our security policy]&#40;../../security/policy&#41; on how to report security vulnerabilities.)

## Credits

- [EriBloo](https://github.com/EriBloo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
