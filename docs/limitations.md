# Limitations

## Known limits

- **One datatable per Livewire component.** The sort and page-length state is a single set of properties on the page. To show two tables on one screen, put each in its own Livewire component and include both.
- **The model needs an `updated_at` column.** [Row order](sorting.md) always includes it. A table without that column fails with an SQL error.
- **Records must be Eloquent models.** The row uses `$record->getKey()`, and `records()` uses the model's key and qualified column names.
- **Renaming or moving a page file resets its saved preferences,** because the key is the Livewire component name. See [Saved preferences](preferences.md).
- **The card height is a fixed number** that depends on the cell padding. See [Layout](layout.md).
- **The top-right corner holds only the Reset button.** A page-specific buttons area is planned, but nothing is built.
- **No search or filters.** None exist yet.
- **A default page length that is not in the options** loads correctly but isn't shown in the select. See [Pagination](pagination.md).
- **A column named `table`, `record`, `attributes` or `slot`** can't be customized with a slot. See [Customizing cells](custom-cells.md).

## Not confirmed in the browser

These were written from the code and haven't been seen working yet:

- `defaultPerPage()` and `perPageOptions()`.
- Saved preferences: the saved sort, page length and page number, the clamp to the last page, and the Reset button. See [Saved preferences](preferences.md).
- `label()`, including `label('')`.
- Slot content other than the bold-email example.

## If something fails

| You see | Check |
|---|---|
| "Class ... not found" | Run `composer dump-autoload`, then reload. |
| An SQL error saying `datatable_preferences` doesn't exist | Publish the migration with `php artisan vendor:publish --tag=datatables-migrations`, then run `php artisan migrate`. See [Quick start](quick-start.md). |
| An SQL error mentioning `updated_at` | The model's table has no `updated_at` column. |
| "Unable to locate a class or view for component" | The tag must be `<x-datatable>`, `<x-datatable.row>` or `<x-datatable.pagination>`, and the files must be in `resources/views/components/datatable/`. |
| An `InvalidArgumentException` naming a column | `sortable()` was given something other than `'asc'` or `'desc'`. See [Columns](columns.md). |
| A scrollbar with only 10 rows | The cell padding or a wrapped cell changed the row height. See [Layout](layout.md). |
| A header click does nothing | The column isn't marked `sortable()`, so the click is ignored on purpose. See [Sorting](sorting.md). |

Previous: [What `HasDatatable` adds to your page](trait-reference.md) | [Back to index](index.md)
