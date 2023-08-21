# Changelog

All notable changes to `laravel-model-snapshots` will be documented in this file.

## 0.4.0 - 2023-09-21

- add description to snapshots
- add options setters to snapshotter
- split snapshot restore method into revert, branch and fork
- separate toModel method on snapshot
- rename methods

## 0.3.0 - 2023-04-11

- morph one snapshot relationship
- better raw values storing to properly handle mutators and accessors
- small properties and methods name changes
- snapshot restoration
- don't create duplicate snapshots by default
- add events

## 0.2.0 - 2023-03-07

- create separate snapshotter class that handles snapshot creation
- snapshot options
- snapshot relations
- multiple smaller changes

## 0.1.0 - 2023-02-01

- initial concept
