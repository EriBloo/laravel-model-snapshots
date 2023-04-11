# Laravel package for storing model versions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eribloo/laravel-model-snapshots.svg?style=flat-square)](https://packagist.org/packages/eribloo/laravel-model-snapshots)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/eribloo/laravel-model-snapshots/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/eribloo/laravel-model-snapshots/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/eribloo/laravel-model-snapshots/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/eribloo/laravel-model-snapshots/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eribloo/laravel-model-snapshots.svg?style=flat-square)](https://packagist.org/packages/eribloo/laravel-model-snapshots)

This package allows creating snapshots of models.

While a typical approach of adding a version column is often enough when there is a need of versioning models,
this package stores snapshots in dedicated table. This provides better control over snapshotting process and keeps your
tables clean.

Package provides simple function for storing model snapshot:

```php
snapshot(Document::find(1))->persist();
```

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
    'snapshot_class' => EriBloo\LaravelModelSnapshots\Models\Snapshot::class,

    /**
     * Versionist class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface interface.
     */
    'versionist_class' => EriBloo\LaravelModelSnapshots\Support\Versionists\IncrementingVersionist::class,

];
```

## Usage

You can create snapshot by using a helper `snapshot()` function:

```php
snapshot(Document::find(1))->persist();
```

This will snapshot model using default options defined in `EriBloo\LaravelModelSnapshots\SnapshotOptions` class:

- set version with Versionist class defined in config
- snapshot all attributes, excluding primary key and hidden
- it won't create snapshot if other snapshot with the same stored attributes already exists

### Snapshot options

Options can be overridden either by defining a `getSnapshotOptions()` method on model:

```php
public function getSnapshotOptions(): SnapshotOptions
{
    return SnapshotOptions::defaults();
}
```

or during snapshot process:

```php
snapshot(Document::find(1))
    ->usingOptions(SnapshotOptions::defaults())
    ->persist();
```

`usingOptions` method accepts SnapshotOptions object or Closure.
If Closure is provided it will receive current options as its first argument.

Configurable options include:

```php
SnapshotOptions::defaults()
    ->withVersionist(new CustomVersionist())
    ->snapshotExcept(['private_attribute'])
    ->snapshotHidden(true)
    ->snapshotDuplicates(true);
```

- `defaults()` - base static method for initializing options
- `withVersionist(VersionistInterface|Closure)` - set Versionist used at runtime, when Closure is provided it will
  receive current Versionist as it's first argument
- `snapshotExcept(array)` - exclude attributes from being stored
- `snapshotHidden(bool)` - store hidden attributes
- `snapshotDuplicates(bool)` - force snapshot even if the same already exists

### Versionist

Versionist is a class responsible for determining next snapshot version.
By default `IncrementingVersionist` is used, which simply increments versions.
There is also simple `SemanticVersionist` available if you want to keep versions in `x.y.z` format.

If you would like to create your own versionist class it must implement
`EriBloo\LaravelModelSnapshots\Contracts\VersionistInterface`. There are two methods you must create:

```php
public function getFirstVersion(): string;

public function getNextVersion(string $version): string;
```

Please note that all snapshots of single model must use the same Versionist class. So in situation like this:

```php
$document = Document::find(1);

snapshot($document)
    ->usingOptions(SnapshotOptions::defaults()->withVersionist(new IncrementingVersionist()))
    ->persist();

snapshot($document)
    ->usingOptions(SnapshotOptions::defaults()->withVersionist(new CustomVersionist()))
    ->persist();
```

an `EriBloo\LaravelModelSnapshots\Exceptions\IncompatibleVersionist` exception would be thrown.

> Note on `SemanticVersionist`

since this class can increment 3 version parts, it cannot be simply set up in config or in `getSnapshotOptions()` method
on model.
(it would result in one part being incremented each time - minor by default, which defeats its purpose).

If you would like to use this Versionist it should be used with `usingOptions` method, eg:

```php
// in Document model
public function getSnapshotOptions(): SnapshotOptions
{
    return SnapshotOptions::defaults()->withVersionist(new SemanticVersionist());
}

// later
snapshot(Document::find(1))
    ->usingOptions(
        fn (SnapshotOptions $options) => $options->withVerionist(
            fn (SemanticVersionist $versionist) => $versionist->incrementMajor()
        )
    )
    ->persist();

// or
snapshot(Document::find(1))
    ->usingOptions(
        fn (SnapshotOptions $options) => $options->withVerionist(
            (new SemanticVersionist)->snapshotMajor()
        )
    )
    ->persist();
```

### Restoring

Snapshots provide `restore()` method that reverts original model to its snapshotted version.

### Traits

While no trait is needed to make a snapshot, package provides 2 helper traits for retrieving snapshots:

- `HasSnapshots` - provides `snapshots()` relationship for retrieving stored snapshots as well as few getters:
    - `getLatestSnapshot()`
    - `getSnapshotByVersion(string $version)` - returns snapshot by specific version
    - `getSnapshotByDate(DateTimeImmutable $date)` - returns last snapshot created before date
- `HasSnapshotRelations` - provides relationship methods for creating connections with snapshots:
    - `morphSnapshots(string $snapshotClass)` - helper `morphToMany`
    - `morphSnapshot(string $snapshotClass)` - helper `morphToOne`
    - `morphSnapshotModels(string $snapshotClass)` - `morphToMany` relation that directly returns snapshotted model
    - `morphSnapshotModel(string $snapshotClass)` - `morphToOne` version of above

### Events

There are 2 events that get dispatched:

- `EriBloo\LaravelModelSnapshots\Events\SnapshotPersisted` - dispatched when new snapshot is persisted, but not when
  duplicate is found
- `EriBloo\LaravelModelSnapshots\Events\SnapshotRestored` - dispatched when snapshot is restored

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
