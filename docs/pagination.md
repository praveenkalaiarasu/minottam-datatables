# Pagination and page length

## Rows per page

Two settings on the [table definition](datatable-definition.md) control this:

```php
Datatable::make()
	->defaultPerPage(10)
	->perPageOptions([10, 25, 50, 100])
```

| Function | Meaning | Default |
|---|---|---|
| `defaultPerPage(int $n)` | How many rows show when the page first loads. | `10` |
| `perPageOptions(array $options)` | The numbers listed in the page-length select. | `[10, 25, 50, 100]` |

You can leave either out. The page-length select, at the bottom left of the table, reads `Show [10] records per page`. Picking a value updates the table straight away and returns you to page 1.

### How the value is validated

The current page length is the public property `$perPage`. Because the browser can change it, `records()` checks it against `perPageOptions`:

- If it is in the options, it is used.
- If it isn't, the table's `defaultPerPage` is used.

### A default that isn't in the options

You can set a `defaultPerPage` that isn't in `perPageOptions`, for example `defaultPerPage(20)` with `perPageOptions([10, 25])`. The table loads with 20 rows per page, but the select has no matching entry, so it shows no selected value. This is intentional and isn't guarded against. Add the number to the options if you want it shown.

## The text and the page buttons

| What | Where | Source |
|---|---|---|
| "Showing X to Y of Z records" | top left | The paginator's first item, last item and total. An empty table reads "Showing 0 to 0 of 0 records". |
| Page-length select | bottom left | `perPageOptions`. |
| Page buttons | bottom right | `<x-datatable.pagination>` |

See [Layout](layout.md) for the corners.

## The page buttons

`<x-datatable.pagination :paginator="$records" />` draws:

- A previous arrow, disabled on the first page.
- Page numbers. Up to 7 pages, every number is shown. With more, a window with `...` gaps: near the start `1 2 3 4 5 ... last`, in the middle `1 ... n-1 n n+1 ... last`, near the end `1 ... last-4 ... last`.
- A next arrow, disabled on the last page.

It draws nothing when there is only one page. `<x-datatable>` already includes it, so you don't add it yourself.

The buttons call `previousPage`, `gotoPage` and `nextPage`, which `HasDatatable` provides through Livewire's pagination. The page number is not written to the URL (the trait also uses Livewire's `WithoutUrlPagination`), so a copied link always opens on the viewer's own saved page. The page is kept per user. See [Saved preferences](preferences.md).

The chosen page length is saved per user. See [Saved preferences](preferences.md).

Previous: [Sorting and row order](sorting.md) | Next: [Saved preferences](preferences.md) | [Back to index](index.md)
