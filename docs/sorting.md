# Sorting and row order

## The three-click cycle

Clicking the header of a [sortable column](columns.md) moves through three states:

1. **First click:** ascending. The up chevron lights up.
2. **Second click:** descending. The down chevron lights up.
3. **Third click:** no sort. Both chevrons fade, and the table goes back to its [fallback order](#the-order-rows-appear-in).
4. **Fourth click:** ascending again, and so on.

Clicking a different sortable column starts that column at ascending, and the first column's chevrons fade.

Every click returns the table to page 1.

The whole header cell is the click target, not just the text.

## Where the state lives

`HasDatatable` adds three public properties to your page:

| Property | Holds |
|---|---|
| `$sortBy` | The sorted column name, or `''` when there is no sort. |
| `$sortDirection` | `'asc'` or `'desc'`. |
| `$perPage` | The current page length. See [Pagination](pagination.md). |

The `sort($column)` method handles header clicks. It ignores any column that isn't marked `sortable()` in the schema.

The sort is saved per user and restored the next time they open the page. See [Saved preferences](preferences.md).

## The order rows appear in

`records($query)` builds the order in this sequence:

1. **The chosen column**, in the chosen direction, if a sort is active.
2. **`updated_at`, newest first.** With no sort active, this is the only ordering, so the table shows the most recently updated rows first.
3. **The primary key, ascending.** This is always added last. `updated_at` can hold the same value for several rows, and without a unique last key, PostgreSQL does not guarantee a stable order between pages. A row could then appear on two pages or be skipped.

Steps 2 and 3 use qualified names (for example `users.updated_at`), so they stay correct if your query joins another table. Step 1 uses the plain column name.

**Requirement:** the model's table must have an `updated_at` column. A table without one fails with an SQL error. See [Limitations](limitations.md).

## Protection against tampered values

Livewire public properties can be changed by the browser. The datatable guards against that:

- `sort()` ignores a column that isn't sortable in the schema.
- `records()` only orders by `$sortBy` if that column is sortable in the schema. Otherwise it skips the chosen column and uses the fallback order.
- A direction other than `desc` is treated as `asc`.

Previous: [Columns](columns.md) | Next: [Pagination and page length](pagination.md) | [Back to index](index.md)
