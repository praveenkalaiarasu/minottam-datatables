# minottam-datatables

Livewire and Flux UI data tables for Laravel.

- Package: `praveenkalaiarasu/minottam-datatables`
- Namespace: `Minottam\Datatables`
- Components: `<x-datatable>`, `<x-datatable.row>`, `<x-datatable.pagination>`

## Requirements

- PHP 8.3+, Laravel 13, Livewire 4
- Flux UI. The Blade components may use Flux Pro components, so the host application needs `livewire/flux-pro` installed.
- A `users` table. The `datatable_preferences` migration references it, with a uuid key by default (see Configuration).

## Install

```bash
composer require praveenkalaiarasu/minottam-datatables
php artisan vendor:publish --tag=datatables-config
php artisan vendor:publish --tag=datatables-migrations
php artisan migrate
```

The service provider is auto-discovered. It registers the Blade components. Publish the config first if you need to change it, because the migration reads it.

## Configuration

`config/minottam-datatables.php`:

- `table`: the preferences table name. Default `datatable_preferences`.
- `user_key_type`: `'uuid'` or `'id'`, the primary key type of your users table. Default `'uuid'`.

## Documentation

See [docs/index.md](docs/index.md).
