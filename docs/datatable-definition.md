# The table definition

`Minottam\Datatables\Datatable` describes one table. You create it in the page's `schema()` method:

```php
protected function schema(): Datatable
{
	return Datatable::make()
		->defaultPerPage(25)
		->perPageOptions([25, 50, 100])
		->columns([
			Column::make('name')->sortable('asc'),
			Column::make('email'),
		]);
}
```

Every function below returns the table itself, so the calls chain in any order.

## Functions you write

| Function | What it sets | Default |
|---|---|---|
| `Datatable::make()` | Creates the table. Always the first call. | |
| `columns(array $columns)` | The list of [`Column`](columns.md) objects, in display order. | none |
| `defaultPerPage(int $n)` | How many rows the table shows when it first loads. | `10` |
| `perPageOptions(array $options)` | The numbers offered in the page-length select. | `[10, 25, 50, 100]` |

If you leave out `defaultPerPage()` or `perPageOptions()`, the defaults in the last column apply. See [Pagination](pagination.md) for how the two interact.

## Functions the system uses

You normally don't call these. The trait and the components do.

| Function | Returns |
|---|---|
| `getColumns()` | The list of columns. |
| `getDefaultPerPage()` | The default page length. |
| `getPerPageOptions()` | The page-length options. |
| `sortBy(string)` / `getSortBy()` | The currently sorted column name, or `''` for no sort. |
| `sortDirection(string)` / `getSortDirection()` | `'asc'` or `'desc'`. |

`HasDatatable` fills in the current sort each time it hands the table to the page (`$this->table`). That is how the header knows which chevron to light up. See [Sorting](sorting.md).

Previous: [Quick start](quick-start.md) | Next: [Columns](columns.md) | [Back to index](index.md)
