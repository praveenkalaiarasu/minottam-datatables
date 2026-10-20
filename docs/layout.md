# Layout

All the markup and styling for the table is in one file:
`resources/views/components/datatable/index.blade.php`

Cell markup is in `row.blade.php` and the page buttons in `pagination.blade.php`, both in the same folder.

## The four corners

The table sits between two rows. Each row has a left and a right area, giving four named corners:

```
┌───────────────────────────┬───────────────────────────┐
│ top-left                  │                top-right  │   ← top row
├───────────────────────────┴───────────────────────────┤
│                        the table                      │
├───────────────────────────┬───────────────────────────┤
│ bottom-left               │             bottom-right  │   ← bottom row
└───────────────────────────┴───────────────────────────┘
```

| Corner | Holds now | See |
|---|---|---|
| top-left | "Showing X to Y of Z records" | [Pagination](pagination.md) |
| top-right | the Reset button; also reserved for a future buttons area | [Saved preferences](preferences.md) |
| bottom-left | "Show [10] records per page" select | [Pagination](pagination.md) |
| bottom-right | the page buttons | [Pagination](pagination.md) |

Each corner is a container with a comment naming it (`{{-- top-left --}}` and so on) in the component file, so you can find it by name.

## The top and bottom rows

- **Gap to the table:** the top row has `pb-5` (20px) below it, and the bottom row has `pt-5` (20px) above it. Both gaps are outside the table card, so they don't affect its height.
- **Reset button:** a Flux button in the top-right corner: `size="sm"`, `variant="filled"`, `icon="arrow-path"`, labelled "Reset". It calls `resetPreferences`. See [Saved preferences](preferences.md).

## The table card

- The table is inside a rounded, bordered card.
- **Fixed height:** the card is `h-[500px]`. It stays that height whatever the number of rows. With fewer rows it shows blank space below, so the bottom row never moves. With more rows than fit, the rows scroll inside the card.
- **The number depends on the cell padding.** 500px is the header plus 10 rows at the current padding (`py-3` on cells), with a few pixels spare so ten rows don't show a scrollbar. If you change the padding, or a cell wraps onto two lines, change this number or the scrollbar will show.
- **Sticky header:** the header row stays at the top while rows scroll under it. It has a solid background: `bg-zinc-50` in light mode and `dark:bg-[#313134]` in dark mode. The dark value was picked by eye to match the theme.

## The header cells

- A [sortable](columns.md) column's whole header cell is a button, so a click anywhere in it sorts.
- The icon at the right edge is two chevrons drawn inline. Unsorted: both faded. [Ascending](sorting.md): the top one is bright. Descending: the bottom one is bright.
- A non-sortable column is plain text with no icon.

## Spacing on the page

`<x-datatable>` accepts normal HTML attributes, so a page can position it:

```blade
<x-datatable class="mt-6" :table="$this->table" :records="$this->users" />
```

The Users page uses this for the gap under the separator.

Previous: [Customizing cells](custom-cells.md) | Next: [What `HasDatatable` adds to your page](trait-reference.md) | [Back to index](index.md)
