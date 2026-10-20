# Datatable

A table for Livewire pages, defined in one place. You describe the table (its columns, which ones sort, how many rows per page) in a single `schema()` method on the page. The datatable then handles sorting, pagination and the page-length control for you, remembers each user's settings between visits, and gives them a Reset button.

This documentation describes the code as it is in the project. Where a behaviour has not yet been confirmed in the browser, the page says so.

## Contents

1. [Quick start](quick-start.md): the smallest working page.
2. [The table definition](datatable-definition.md): `Datatable::make()` and its chain functions.
3. [Columns](columns.md): `Column::make()`, `IndexColumn::make()`, `sortable()` and the default sort.
4. [Sorting and row order](sorting.md): the three-click cycle and the fallback order.
5. [Pagination and page length](pagination.md): rows per page, the options, the paginator.
6. [Saved preferences](preferences.md): the sort and page length kept per user.
7. [Customizing cells](custom-cells.md): slots per column.
8. [Layout](layout.md): the four corners, the fixed height, the sticky header.
9. [What `HasDatatable` adds to your page](trait-reference.md): every property and method, and the names to avoid.
10. [Limitations](limitations.md): known limits and what to check when something fails.

## The pieces

| Piece | File | What it does |
|---|---|---|
| `HasDatatable` | `src/HasDatatable.php` | A trait you add to a Livewire page. It keeps the sort, page-length and page state, saves it per user, handles clicks on headers and the Reset button, and gives you `records()`. See [the full list](trait-reference.md). |
| `Datatable` | `src/Datatable.php` | The table definition: columns, default page length, page-length options. Also carries the current sort so the component can draw it. |
| `Column` | `src/Column.php` | One column: its name, its label, whether it sorts, and its default sort direction. |
| `IndexColumn` | `src/IndexColumn.php` | A `Column` that shows a serial number under the header `#`. |
| `<x-datatable>` | `resources/views/components/datatable/index.blade.php` | Draws the whole table: the top row, the header, the rows, the bottom row. |
| `<x-datatable.row>` | `resources/views/components/datatable/row.blade.php` | Draws one row. Used automatically, or by you when you [customize cells](custom-cells.md). |
| `<x-datatable.pagination>` | `resources/views/components/datatable/pagination.blade.php` | The page-number buttons. Used by `<x-datatable>`. |
| `DatatablePreference` | `src/Models/DatatablePreference.php` | The model for saved preferences. See [Saved preferences](preferences.md). |
| The migration | `database/migrations/0001_01_03_100001_create_datatable_preferences_table.php` | Creates the preferences table. Publish it with `php artisan vendor:publish --tag=datatables-migrations`, then run it before any page uses `HasDatatable`. |

## How they connect

```
Your page (Livewire component)
  uses HasDatatable
  defines schema()  ────────────►  Datatable  ──►  Column, Column, ...
  defines a computed property  ──►  $this->records($query)  ──►  a page of rows
  markup:  <x-datatable :table="$this->table" :records="$this->users" />
                │
                ├── top row         ("Showing X to Y of Z records" text + Reset button)
                ├── header cells    (from the Columns)
                ├── rows            (<x-datatable.row>, one per record)
                └── bottom row      (page length select + <x-datatable.pagination>)
```

- `$this->table` is a computed property supplied by `HasDatatable`. It returns your `schema()` with the current sort filled in.
- `$this->records($query)` is a method supplied by `HasDatatable`. You pass it an Eloquent query, and it returns one page of rows, already sorted.
- Clicks on headers, page buttons and the page-length select go straight to the page component, because the Blade components are drawn inside the page.

## Rules in one list

- Publish the migration (`php artisan vendor:publish --tag=datatables-migrations`) and run `php artisan migrate` before using `HasDatatable`. The trait reads and writes the `datatable_preferences` table every time a table page loads, so a page fails without it.
- One datatable per Livewire component. To show two tables on one screen, use two components. See [Limitations](limitations.md).
- The model behind the table needs an `updated_at` column. See [Sorting](sorting.md).
- The records must be Eloquent models.

Next: [Quick start](quick-start.md)
