# Columns

`Minottam\Datatables\Column` describes one column. You list columns inside [`columns([...])`](datatable-definition.md):

```php
->columns([
	Column::make('name')->sortable('asc'),
	Column::make('email')->sortable(),
	Column::make('role'),
])
```

## `Column::make(string $name)`

Creates a column. `$name` is the attribute read from each record, so `make('email')` prints `$record->email`. It is also the column used when sorting, so it must be a real database column.

## `label(string $label)`

Sets the header text. It changes only what the header shows. The `make()` name is still used to read the value and to sort.

```php
Column::make('email')->label('E-mail address')
Column::make('actions')->label('')
```

If you don't call `label()`, the header text is made from the `make()` name with `Str::headline()`:

| Name | Header |
|---|---|
| `name` | Name |
| `email` | Email |
| `created_at` | Created At |

`label('')` is kept as an empty header, for example for an actions column. Only a column with no `label()` call at all gets the generated text.

## `sortable()`

Makes the column sortable. A sortable column gets a header you can click anywhere in, with a two-chevron icon at the right edge. See [Sorting](sorting.md) for what the clicks do.

`sortable()` takes one optional argument, the default direction:

| Call | Meaning |
|---|---|
| `->sortable()` | Sortable, but the table doesn't start sorted by it. |
| `->sortable('asc')` | Sortable, and the table starts sorted by this column, ascending. |
| `->sortable('desc')` | Sortable, and the table starts sorted by this column, descending. |
| (not called) | Not sortable. The header is plain text with no click and no icon. |

Any other argument, for example `'des'`, throws an `InvalidArgumentException` that names the column and the bad value.

### What if no column has a default, or several do?

- **None has a default:** the table loads with no sort. Clicking a header still sorts. See [Sorting](sorting.md) for the order rows appear in.
- **More than one has a default:** the last one in the list wins.

The default is applied once, when the page first loads. If you click your way to "no sort" later, it stays unsorted until the page is loaded again.

## `IndexColumn::make()`

`Minottam\Datatables\IndexColumn` adds a serial-number column. It takes no arguments, has the header `#`, and is not sortable. Put it first in the list to get a first column:

```php
->columns([
	IndexColumn::make(),
	Column::make('name')->sortable(),
])
```

The numbers continue across pages: with 10 records per page, page 2 starts at 11. The number comes from the `loop-index` the row receives, so a custom row loop must pass it. See [Customizing cells](custom-cells.md). A row that isn't given `loop-index` leaves the `#` cell empty.

## Functions the system uses

| Function | Returns |
|---|---|
| `name()` | The column name. |
| `getLabel()` | The header text: the `label()` value if one was set, otherwise the generated text. |
| `isIndex()` | `true` for an `IndexColumn`, otherwise `false`. |
| `isSortable()` | `true` if `sortable()` was called. |
| `defaultDirection()` | `'asc'`, `'desc'` or `null`. |

Previous: [The table definition](datatable-definition.md) | Next: [Sorting and row order](sorting.md) | [Back to index](index.md)
