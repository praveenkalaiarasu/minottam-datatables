# Quick start

The smallest working datatable page. It is the Users page, trimmed to what you need.

## Before you start

Run the migration once, so the table that stores saved preferences exists:

```bash
php artisan migrate
```

`HasDatatable` reads and writes that table whenever a table page loads, so a page fails without it. See [Saved preferences](preferences.md).

## 1. The page class

```php
<?php

use Minottam\Datatables\Column;
use Minottam\Datatables\Datatable;
use Minottam\Datatables\HasDatatable;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::panel'), Title('Users')]
class extends Component {

	use HasDatatable;

	protected function schema(): Datatable
	{
		return Datatable::make()
			->columns([
				Column::make('name')->sortable('asc'),
				Column::make('email'),
			]);
	}

	#[Computed]
	public function users()
	{
		return $this->records(User::query());
	}

};
?>
```

What each part does:

- `use HasDatatable;` adds the sort and page-length state, the sort click handler, and the `table` and `records()` helpers. See [the overview](index.md).
- `schema()` describes the table. It must return a [`Datatable`](datatable-definition.md). It is `protected` so the browser can't call it. The `Column`s inside are explained in [Columns](columns.md).
- `users()` is a computed property that you name yourself (`users`, `enterprises`, and so on). It passes an Eloquent query to `$this->records()`. `records()` adds the sort and the pagination and returns a page of rows. See [Sorting](sorting.md) and [Pagination](pagination.md).

## 2. The markup

The simplest form:

```blade
<x-datatable :table="$this->table" :records="$this->users" />
```

- `:table="$this->table"` is the definition, with the current sort set on it.
- `:records="$this->users"` is the page of rows from your computed property.

With nothing between the tags, the component draws every row and every cell by itself, printing each column's plain value.

To change how some cells look, write the loop yourself. See [Customizing cells](custom-cells.md).

## 3. What you get without writing anything else

- Clickable, sortable headers for the columns marked `sortable()`.
- A "Showing X to Y of Z records" text at the top left.
- A Reset button at the top right, which clears the user's saved settings.
- A page-length select at the bottom left.
- The page buttons at the bottom right.
- The sort, page length and page number remembered for each user between visits. See [Saved preferences](preferences.md).

See [Layout](layout.md) for where each appears.

Previous: [Overview](index.md) | Next: [The table definition](datatable-definition.md)
