# minottam-datatables

Livewire and Flux UI data tables for Laravel.

- Package: `praveenkalaiarasu/minottam-datatables`
- Namespace: `Minottam\Datatables`
- Components: `<x-datatable>`, `<x-datatable.row>`, `<x-datatable.pagination>`

## Requirements

- PHP 8.3+, Laravel 13, Livewire 4
- Flux UI. The Blade components may use Flux Pro components, so the host application needs `livewire/flux-pro` installed.
- A `users` table with a uuid primary key. The `datatable_preferences` migration references it.

## Install

```bash
composer require praveenkalaiarasu/minottam-datatables
php artisan migrate
```

The service provider is auto-discovered. It registers the Blade components and loads the migration.

## Documentation

See [docs/index.md](docs/index.md).
