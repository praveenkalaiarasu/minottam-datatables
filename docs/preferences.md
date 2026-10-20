# Saved preferences

The datatable remembers each user's sort and page length. When they come back to the page (after a refresh, after navigating away, or after logging in again) the table opens the way they left it.

## What is saved

| Setting | Key in the saved data | Restored when |
|---|---|---|
| Sorted column | `sort_by` | The column is still [sortable](columns.md) in the schema. |
| Sort direction | `sort_direction` | It is `asc` or `desc`. |
| Rows per page | `per_page` | It is still in the table's [`perPageOptions`](pagination.md). |
| Current page | `page` | It is a whole number of 1 or more. If it is past the last page, the last page is shown instead. |

The page number is saved every time the page changes (next, previous, a page number, or the reset to page 1 after a sort or a page-length change). It is never written to the URL.

If a saved page no longer exists (rows were deleted, or the page length grew), the table shows the last real page. The stale number stays in the database row until the next change. Each load clamps it again, and nothing is written during the render.

A saved sort of `''` (empty) means the user clicked through to "no sort". It is saved and restored like any other value, so a refresh keeps the table unsorted even if the schema has a default sort.

## Where it is saved

In the database table `datatable_preferences`, one row per user per table:

| Column | Holds |
|---|---|
| `user_id` | The logged-in user. Deleting a user deletes their rows. |
| `table_key` | Which table. See below. |
| `preferences` | A `jsonb` value, for example `{"sort_by":"name","sort_direction":"desc","per_page":25}`. |

The model is `Minottam\Datatables\Models\DatatablePreference`. The table is created by `database/migrations/0001_01_03_100001_create_datatable_preferences_table.php`.

Because the settings are stored as one JSON value, adding a new saved setting later does not need a new migration.

## Which table a row belongs to

`table_key` is the Livewire component's own name, from `$this->getName()`. For the Users page it is `pages::panel.users-management.users.index`. You don't configure anything on a page.

The consequence: **renaming or moving a page file changes its key**, so that page starts from its defaults and the old row is left unused.

## When it is loaded and saved

- **Loaded once**, when the page first loads: the schema's defaults are applied first (the default per-page and any `sortable('asc')` default sort), then the saved values are applied on top. Saved values win over the defaults.
- **Saved** when the user clicks a sort header, when they change the page length, and when the page changes. A sort click or a page-length change also resets to page 1, so each of those writes twice (the setting, then page 1).
- A save keeps the other values already in the row, so saving the sort does not erase the page length.
- Nobody logged in: nothing is loaded or saved.

## Stale values are ignored

A saved value is checked against the current schema before it is used. If a column was removed or made non-sortable, or a page length was removed from the options, that saved value is skipped and the schema's default is used.

The same check protects against tampered values: a page length that is not in the options is not saved.

## Resetting

The **Reset** button in the top-right corner of the table clears the saved preferences and returns the table to its starting state:

- The user's row in `datatable_preferences` for this table is deleted.
- The sort and the page length go back to the schema's defaults (the default per-page, and the column marked `sortable('asc')` or `sortable('desc')`, if any).
- The table goes to page 1.

No row is left behind afterwards. A new row appears only when the user next changes a setting. Anything stored in the row later (search, visible columns) is cleared by the same button.

## Cost

One database read when the page loads. One write (a single PostgreSQL upsert) each time the user sorts, changes the page length or changes the page. The upsert inserts the row if it is missing, or merges the new keys into the stored `jsonb` if it exists, in one atomic statement, so there is no separate read before the write and no race between two quick changes. It is a raw `INSERT ... ON CONFLICT (user_id, table_key) DO UPDATE` using bound parameters, because Eloquent's `upsert()` cannot merge `jsonb`. This ties the saving to PostgreSQL.

## Unverified

This was written from the code and has not yet been confirmed in the browser.

Previous: [Pagination and page length](pagination.md) | Next: [Customizing cells](custom-cells.md) | [Back to index](index.md)
