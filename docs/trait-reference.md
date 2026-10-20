# What `HasDatatable` adds to your page

When you write `use HasDatatable;` in a page, the page gains the properties and methods listed here. Don't give anything on your own page one of these names, or the two will clash.

The trait also uses Livewire's `WithPagination` and `WithoutUrlPagination`, so the page also gains Livewire's pagination members, listed at the end.

## You must define

| Member | Notes |
|---|---|
| `schema(): Datatable` | `protected`. Returns the [table definition](datatable-definition.md). The trait declares it as abstract, so a page without it fails. |

You also write one computed property of your own (for example `users()`) that calls `$this->records($query)`. See [Quick start](quick-start.md).

## Public properties

Livewire remembers these between requests. The browser can change them, so every use is validated. See [Sorting](sorting.md) and [Pagination](pagination.md).

| Property | Holds |
|---|---|
| `$sortBy` | The sorted column name, or `''` for no sort. |
| `$sortDirection` | `'asc'` or `'desc'`. |
| `$perPage` | The current rows per page. |

## Public methods

| Method | What it does |
|---|---|
| `table()` | A computed property (`$this->table`). Returns `schema()` with the current sort set on it. You pass it to `<x-datatable>`. |
| `sort($column)` | Called by a header click. Cycles ascending, descending, none, and saves it. Ignores columns that aren't sortable. See [Sorting](sorting.md). |
| `resetPreferences()` | Called by the Reset button. Deletes the saved row and returns to the schema defaults and page 1. See [Saved preferences](preferences.md). |
| `mountHasDatatable()` | Runs automatically when the page first loads. Applies the schema defaults, then the saved preferences. |
| `updatedPerPage()` | A Livewire hook, run when the page-length select changes. Saves the value and returns to page 1. |
| `updatedPaginators($page, $pageName)` | A Livewire hook, run on every page change. Saves the page number. |

Livewire lets the browser call any public method on a page. `sort()` and `resetPreferences()` are meant to be called that way. The others are hooks Livewire runs itself.

## Protected methods

These are internal. They can't be called from the browser.

| Method | What it does |
|---|---|
| `records($query)` | Takes your Eloquent query and returns one page of rows: sorted, ordered by the [fallback order](sorting.md), paginated, and clamped to the last real page. |
| `applyDefaults()` | Sets the sort and page length from the schema. |
| `isSortable($column)` | Whether a column is marked `sortable()` in the schema. |
| `preferenceKey()` | The user id and the Livewire component name used to find the saved row. |
| `loadPreferences()` | Reads this user's saved row, or returns an empty array. |
| `savePreference(array $values)` | Merges values into this user's saved row with a single PostgreSQL upsert (insert if missing, jsonb merge if present). |

## Livewire pagination members

From `WithPagination`. The page buttons call these:

| Member | What it does |
|---|---|
| `$paginators` | A public array holding the current page number. |
| `previousPage()`, `nextPage()`, `gotoPage($page)` | Move between pages. |
| `resetPage()`, `setPage($page)`, `getPage()` | Reset, set and read the page. |

`WithoutUrlPagination` keeps the page number out of the URL. See [Pagination](pagination.md).

Previous: [Layout](layout.md) | Next: [Limitations](limitations.md) | [Back to index](index.md)
