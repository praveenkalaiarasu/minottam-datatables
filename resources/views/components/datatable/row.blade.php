@aware(['table', 'records'])

@props(['record', 'loopIndex' => null])

<tr wire:key="row-{{ $record->getKey() }}">
	@foreach ($table->getColumns() as $column)
		@php($cell = ${$column->name()} ?? null)

		<td class="px-3 py-3 text-zinc-800 dark:text-white">
			@if ($column->isIndex())
				{{ $loopIndex === null ? '' : $records->firstItem() + $loopIndex }}
			@elseif ($column->isAction())
				@if ($cell instanceof \Illuminate\View\ComponentSlot)
					{{ $cell }}
				@endif
			@elseif ($cell instanceof \Illuminate\View\ComponentSlot && trim((string) $cell) !== '')
				{{ $cell }}
			@else
				{{ $column->formatValue($record->{$column->name()}) }}
			@endif
		</td>
	@endforeach
</tr>
