# Laravel package for storing model versions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eribloo/laravel-model-snapshots.svg?style=flat-square)](https://packagist.org/packages/eribloo/laravel-model-snapshots)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/eribloo/laravel-model-snapshots/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/eribloo/laravel-model-snapshots/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/eribloo/laravel-model-snapshots/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/eribloo/laravel-model-snapshots/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/eribloo/laravel-model-snapshots.svg?style=flat-square)](https://packagist.org/packages/eribloo/laravel-model-snapshots)

## Introduction

This package allows creating snapshots of models.

While a typical approach of adding a version column is often enough when there is a need of versioning models,
this package stores snapshots in dedicated table. This provides better control over snapshotting process and keeps your
tables clean.

My motivation while creating this package was to create configurable snapshots only when I need them, in contrast to
generating new version with every update, while keeping connection to up-to-date original model.

## Table of contents

- [Installation](#installation)
- [Examples](#examples)
- [Usage](#usage)
    - [Basics](#basics)
        - [Creating snapshots](#creating-snapshots)
        - [Reverting, branching and forking snapshots](#reverting-branching-and-forking-snapshots)
        - [Relations](#relations)
    - [Configuring](#configuring)
        - [Snapshot Options](#snapshot-options)
        - [Versionist](#versionist)
    - [Traits](#traits)
    - [Events](#events)
- [Testing](#testing)
- [Changelog](#changelog)
- [Licence](#license)

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
     * Snapshot class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\Snapshot interface.
     */
    'snapshot_class' => EriBloo\LaravelModelSnapshots\Models\Snapshot::class,

    /**
     * Versionist class used. Must implement EriBloo\LaravelModelSnapshots\Contracts\Versionist interface.
     */
    'versionist_class' => EriBloo\LaravelModelSnapshots\Support\Versionists\IncrementingVersionist::class,

];
```

## Usage

### Basics

#### Creating snapshots

You can create snapshot by using a helper `snapshot()` function:

```php
snapshot(Document::find(1))->commit();
```

This will snapshot model using default options defined in `EriBloo\LaravelModelSnapshots\SnapshotOptions` class:

- set version with Versionist class defined in config
- snapshot all attributes, excluding primary key, timestamps and hidden
- it won't create snapshot if other snapshot with the same stored attributes already exists - in such situation matching
  snapshot will be returned

Each snapshot stores an array of model attributes, options that it was created with, version and optional description.

Snapshots provide `toModel(bool $fillExcludedAttributes = false)` method, that returns model filled with
snapshotted attributes. If optional `fillExcludedAttributes` option is true, returned model will use current model
attributes as a base, otherwise missing attributes will be null.

Accordingly, if you retrieve collection of snapshots you can use its `toModels(bool $fillExcludedAttributes = false)`
method to map all snapshots to corresponding classes.

#### Reverting, branching and forking snapshots

Snapshots have 3 helper methods to revert model, or to create a new one, from its snapshot:

- `revert()` - reverts original model to its snapshotted version, all snapshots created after the one used are deleted
- `branch()` - creates new model from snapshotted version and duplicates all snapshots up to, and including, the one
  used, associating them with new model
- `fork()` - creates new model from snapshotted version with no snapshots history

Attributes excluded from snapshotting will be filled with current model values.

#### Relations

In addition, package provides separate table to store snapshot relations with other models. There are morphToMany and
morphToOne relations available that return either Snapshots or Models in `HasSnapshotRelations` [trait](#traits).

### Configuring

#### Snapshot options

Options can be defined by creating `getSnapshotOptions()` method on model:

```php
public function getSnapshotOptions(): SnapshotOptions
{
    return SnapshotOptions::defaults();
}
```

Configurable options include:

- `withVersionist(Versionist $versionist)` - set [Versionist](#versionist) used
- `snapshotExcept(array $exclude)` - exclude attributes from being stored
- `snapshotHidden(bool $option = true)` - store hidden attributes
- `snapshotDuplicate(bool $option = true)` - force snapshot even if the same already exists

Most can be later overridden while snapshotting using those methods:

- `version(Closure $closure)` - Closure that will receive current Versionist object, so you can access and call its
  methods if needed
- `description(?string)` - add optional short description
- `setExcept(array $except)`, `appendExcept(array $except)`, `removeExcept(array $except)` - modify excluded attributes
  list
- `withHidden()`, `withoutHidden()` - modify if hidden attributes should be snapshotted
- `forceDuplicate()`, `noDuplicate()` - if snapshot should be forced even if duplicate already exists

#### Versionist

Versionist is a class responsible for determining next snapshot version.
There are 2 classes available by default:

- `IncrementingVersionist` - increments versions
- `SemanticVersionist` - keeps versions in `major.minor` format

If you would like to create your own versionist class it must implement
`EriBloo\LaravelModelSnapshots\Contracts\Versionist` with methods:

```php
public function getFirstVersion(): string;

public function getNextVersion(string $version): string;
```

### Traits

While no trait is needed to make a snapshot, package provides 2 helper traits for retrieving snapshots:

- `HasSnapshots` - provides `snapshots()` relationship for retrieving stored snapshots as well as few getters:
    - `getLatestSnapshot()`
    - `getSnapshotByVersion(string $version)` - returns snapshot by specific version
    - `getSnapshotByDate(DateTimeImmutable $date)` - returns last snapshot created before date
- `HasSnapshotRelations` - provides relationship methods for creating connections with snapshots:
    - `morphSnapshots(string $snapshotClass)` - helper `morphToMany`
    - `morphSnapshot(string $snapshotClass)` - helper `morphToOne`
    - `morphSnapshotAsModels(string $snapshotClass)` - `morphToMany` that returns snapshots with `toModels(false)`
      applied
    - `morphSnapshotAsModel(string $snapshotClass)` - `morphToOne` that returns snapshot with `toModel(false)`
      applied

### Events

There are a few events that get dispatched:

- `SnapshotCommitted` - dispatched when new snapshot is committed, but not when duplicate is found
- `SnapshotReverted` - dispatched when snapshot is reverted
- `SnapshotBranched` - dispatched when new snapshot branch is created
- `SnapshotForked` - dispatched when snapshot is forked

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

[//]: # (## Credits)

[//]: # (- [EriBloo]&#40;https://github.com/EriBloo&#41;)

[//]: # (- [All Contributors]&#40;../../contributors&#41;)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
