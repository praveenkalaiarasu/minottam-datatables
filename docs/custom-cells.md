# Customizing cells

By default `<x-datatable>` prints each cell's plain value. To change how particular cells look (bold, a link, a badge), you write the row loop yourself and give the row a slot named after the column.

## Example

```blade
<x-datatable :table="$this->table" :records="$this->users">
	@foreach ($this->users as $index => $user)
		<x-datatable.row :record="$user" :loop-index="$index">
			<x-slot:email>
				<strong>{{ $user->email }}</strong>
			</x-slot:email>
		</x-datatable.row>
	@endforeach
</x-datatable>
```

- `<x-slot:email>` is a slot named after the column `email`. Its content replaces that cell.
- The `name` column has no slot, so it prints the plain value, `$user->name`.
- `$user` inside the slot is the loop variable you chose in your own `@foreach`, so any attribute or method of the record is available.
- You name the variable yourself: `$user`, `$enterprise`, and so on.

## Rules

- **Content between the `<x-datatable>` tags replaces the default rows.** If there is no content, the component draws the rows itself. If you write a loop, you must write all of it.
- **`<x-datatable.row>` takes `:record` and `:loop-index`.** It finds the table and the records by itself from the `<x-datatable>` around it. `:loop-index` is the key of your `@foreach` (`$index => $user`); it is used only by an [`IndexColumn`](columns.md), which leaves its cell empty if `:loop-index` is missing.
- **A slot is used only if it has content.** An empty slot falls back to the plain value.
- **Use the same records variable** in your loop that you pass to `:records`, so the rows match what the paginator counts.

## Column names you can't give a slot

The row component already uses a few variable names, so a column with one of these names can't be overridden with a slot:

| Column name | What happens |
|---|---|
| `table`, `record`, `attributes` | The slot is ignored, and the plain value prints. |
| `slot` | Avoid it. A column called `slot` would pick up the row's default slot content. |

Ordinary names such as `name`, `email` and `role` are fine.

## Unverified

The bold-email example above is the one used on the Users page, and it has been seen working there. Other kinds of slot content (links, badges, components) follow the same mechanism but haven't been tried.

Previous: [Saved preferences](preferences.md) | Next: [Layout](layout.md) | [Back to index](index.md)
