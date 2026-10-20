@props(['table', 'records'])

@php
	$filterColumns = collect($table->getColumns())
		->filter(fn ($column) => $column->isFilterable())
		->mapWithKeys(fn ($column) => [$column->name() => [
			'label' => $column->getLabel(),
			'inputType' => $column->inputType(),
			'operators' => $column->operators(),
			'valueless' => collect(array_keys($column->operators()))->reject(fn ($operator) => $column->operatorNeedsValue($operator))->values()->all(),
			'ranged' => collect(array_keys($column->operators()))->filter(fn ($operator) => $column->operatorNeedsSecondValue($operator))->values()->all(),
			'options' => $column->getOptions(),
			'prefix' => $column->getPrefix() ?? '',
			'suffix' => $column->getSuffix() ?? '',
		]]);
@endphp

<div {{ $attributes }}>

	{{-- Control bar --}}
	<div class="mb-4 flex items-center gap-2 rounded-lg px-2 py-2 border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
		<div class="flex flex-1 flex-wrap items-center gap-2">
			<flux:modal.trigger name="datatable-filter">
				<flux:button size="sm" variant="ghost" icon="funnel">Filter</flux:button>
			</flux:modal.trigger>

			@foreach ($table->getFilters() as $index => $filter)
				@php
					$filterColumn = $filterColumns->get($filter['column']);
					$operatorLabel = $filterColumn['operators'][$filter['operator']] ?? $filter['operator'];
					$chipColumn = collect($table->getColumns())->first(fn ($column) => $column->name() === $filter['column']);
						$formatFilterValue = fn ($value) => trim(($filterColumn['prefix'] ?? '').($filterColumn['options'][$value] ?? $chipColumn?->formatFilterValue((string) $value) ?? $value).($filterColumn['suffix'] ?? ''));
				@endphp

				<span wire:key="filter-chip-{{ $index }}" class="inline-flex items-center gap-1 rounded-md bg-zinc-200 py-1 pe-1 ps-2 text-xs text-zinc-800 dark:bg-white/10 dark:text-white">
					<span>{{ $filterColumn['label'] ?? $filter['column'] }} {{ \Illuminate\Support\Str::lower($operatorLabel) }}@if (! in_array($filter['operator'], $filterColumn['valueless'] ?? [], true)) "{{ $formatFilterValue($filter['value']) }}"@endif @if (in_array($filter['operator'], $filterColumn['ranged'] ?? [], true)) and "{{ $formatFilterValue($filter['value2'] ?? '') }}"@endif</span>
					<button type="button" wire:click="removeFilter({{ $index }})" class="cursor-pointer rounded p-0.5 hover:bg-zinc-300 dark:hover:bg-white/10" aria-label="Remove filter">
						<flux:icon.x-mark variant="micro" />
					</button>
				</span>
			@endforeach

			@if (count($table->getFilters()) > 0)
				<flux:button size="xs" variant="subtle" wire:click="clearFilters">Clear all</flux:button>
			@endif
		</div>

		<flux:separator vertical class="my-1" />

		<flux:button size="sm" variant="ghost" icon="eye" />
	</div>

	<div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-white/10">

		{{-- Top row --}}
		<div class="flex items-center justify-between gap-4 border-b border-zinc-200 px-3 py-3 dark:border-white/10 bg-zinc-50 dark:bg-zinc-900/50">
			<div>
				{{-- top-left --}}
				<p class="text-sm text-zinc-500 dark:text-zinc-400">
					@if ($table->getUnfilteredTotal() === null)
						Showing {{ $records->firstItem() ?? 0 }} to {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} records
					@elseif ($records->total() === 0)
						Showing 0 filtered from {{ $table->getUnfilteredTotal() }} records
					@else
						Showing {{ $records->firstItem() ?? 0 }} to {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} filtered from {{ $table->getUnfilteredTotal() }} records
					@endif
				</p>
			</div>

			<div>
				{{-- top-right --}}
				<flux:button size="sm" variant="filled" icon="arrow-path" wire:click="resetPreferences">Reset</flux:button>
			</div>
		</div>

		<div class="{{ $records->perPage() > 10 ? 'max-h-[545px]' : '' }} overflow-auto">
			<table class="w-full table-fixed text-left text-sm">
				<thead class="sticky top-0 z-10 border-b border-zinc-200 dark:border-white/10 bg-taupe-50 dark:bg-zinc-900">
					<tr>
						@foreach ($table->getColumns() as $column)
							<th @if ($column->getWidth()) style="width: {{ $column->getWidth() }}" @endif class="{{ $column->isSortable() ? 'p-0' : 'px-3 py-3' }} font-medium text-zinc-500 dark:text-zinc-400">
								@if ($column->isSortable())
									@php
										$isSorted = $table->getSortBy() === $column->name();
										$isAsc = $isSorted && $table->getSortDirection() === 'asc';
										$isDesc = $isSorted && $table->getSortDirection() === 'desc';
									@endphp

									<button type="button" wire:click="sort('{{ $column->name() }}')" class="flex w-full cursor-pointer items-center justify-between gap-1 px-3 py-3 text-left">
										{{ $column->getLabel() }}
										<svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
											<path d="m8 9 4-4 4 4" class="{{ $isAsc ? 'stroke-zinc-900 dark:stroke-white' : 'opacity-40' }}" />
											<path d="m16 15-4 4-4-4" class="{{ $isDesc ? 'stroke-zinc-900 dark:stroke-white' : 'opacity-40' }}" />
										</svg>
									</button>
								@else
									{{ $column->getLabel() }}
								@endif
							</th>
						@endforeach
					</tr>
				</thead>

				<tbody class="divide-y divide-zinc-300 dark:divide-white/5 bg-white dark:bg-zinc-900/25">
					@if (trim((string) $slot) === '')
						@foreach ($records as $index => $record)
							<x-datatable.row :record="$record" :loop-index="$index" />
						@endforeach
					@else
						{{ $slot }}
					@endif

					@if ($records->count() === 0)
						<tr>
							<td colspan="{{ count($table->getColumns()) }}" class="px-3 py-12 text-center text-zinc-500 dark:text-zinc-400">
								No records to show.
							</td>
						</tr>
					@endif
				</tbody>
			</table>
		</div>

		{{-- Bottom row --}}
		<div class="flex items-center justify-between gap-4 px-3 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
			<div>
				{{-- bottom-left --}}
				<div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
					<span>Records per page</span>

					<flux:select size="xs" wire:model.live="perPage" class="w-20">
						@foreach ($table->getPerPageOptions() as $length)
							<flux:select.option :value="$length">{{ $length }}</flux:select.option>
						@endforeach
					</flux:select>
				</div>
			</div>

			<div>
				{{-- bottom-right --}}
				<x-datatable.pagination :paginator="$records" />
			</div>
		</div>

	</div>

	{{-- Filter modal --}}
	<flux:modal
		name="datatable-filter"
		:dismissible="false"
		class="w-full max-w-md backdrop:backdrop-blur-sm"
		x-data="{
			page: 'columns',
			column: '',
			operator: '',
			value: '',
			value2: '',
			columns: {{ \Illuminate\Support\Js::from($filterColumns) }},
			get label() { return this.columns[this.column]?.label ?? '' },
			get operators() { return this.columns[this.column]?.operators ?? {} },
			get inputType() { return this.columns[this.column]?.inputType ?? 'text' },
			get isSelect() { return this.inputType === 'select' },
			get options() { return this.columns[this.column]?.options ?? {} },
			get prefix() { return (this.columns[this.column]?.prefix ?? '').trim() },
			get suffix() { return (this.columns[this.column]?.suffix ?? '').trim() },
			get supported() { return Object.keys(this.operators).length > 0 },
			get needsValue() { return ! (this.columns[this.column]?.valueless ?? []).includes(this.operator) },
			get needsSecondValue() { return (this.columns[this.column]?.ranged ?? []).includes(this.operator) },
			get ready() { return this.operator !== '' && (! this.needsValue || (this.value.trim() !== '' && (! this.needsSecondValue || this.value2.trim() !== ''))) },
			pick(name) {
				this.column = name;
				this.value = '';
				this.value2 = '';
				this.operator = Object.keys(this.operators)[0] ?? '';
				this.page = 'filter';
			},
			reset() {
				this.page = 'columns';
				this.column = '';
				this.operator = '';
				this.value = '';
				this.value2 = '';
			},
			apply() {
				if (! this.ready) return;
				$wire.applyFilter(this.column, this.operator, this.value, this.value2).then((applied) => { if (applied) $flux.modal('datatable-filter').close() });
			},
		}"
		x-on:close="reset()"
	>
		{{-- Page 1: select column --}}
		<div x-show="page === 'columns'" class="space-y-6">
			<div>
				<flux:heading size="lg">Select Column to Filter</flux:heading>
				<flux:text class="mt-1">Select a column below to configure filtering.</flux:text>
			</div>

			<flux:separator />

			<div class="space-y-2">
				@foreach ($filterColumns as $name => $info)
					<button type="button" x-on:click="pick(@js($name))" class="flex w-full cursor-pointer items-center justify-between rounded-lg bg-zinc-100 px-4 py-3 text-left text-sm font-medium text-zinc-800 hover:bg-zinc-200 dark:bg-white/5 dark:text-white dark:hover:bg-white/10">
						{{ $info['label'] }}
						<flux:icon.chevron-right variant="micro" />
					</button>
				@endforeach

				@if ($filterColumns->isEmpty())
					<flux:text>No filterable columns.</flux:text>
				@endif
			</div>

			<flux:separator />

			<div class="flex">
				<flux:spacer />
				<flux:modal.close>
					<flux:button variant="ghost">Close</flux:button>
				</flux:modal.close>
			</div>
		</div>

		{{-- Page 2: set filter --}}
		<div x-show="page === 'filter'" x-cloak class="space-y-6">
			<div class="flex items-center gap-2">
				<flux:button size="sm" variant="ghost" icon="chevron-left" x-on:click="page = 'columns'" />
				<flux:heading size="lg">Set Filter: <span x-text="label"></span></flux:heading>
			</div>

			<flux:separator />

			<div class="min-h-24 space-y-4">
				<div x-show="! supported" class="flex min-h-24 items-center justify-center rounded-lg border-2 border-dashed border-zinc-300 p-6 text-center dark:border-white/20">
					<flux:text>This column type does not support filtering.</flux:text>
				</div>

				<flux:field x-show="supported">
					<flux:label>Condition</flux:label>
					<flux:select x-model="operator">
						<template x-for="(operatorLabel, operatorKey) in operators" x-bind:key="operatorKey">
							<option x-bind:value="operatorKey" x-text="operatorLabel"></option>
						</template>
					</flux:select>
				</flux:field>

				<flux:field x-show="supported && needsValue">
					<flux:label>Filter value</flux:label>
					<flux:select x-show="isSelect" x-model="value">
						<option value="">Select an option</option>
						<template x-for="(optionLabel, optionKey) in options" x-bind:key="optionKey">
							<option x-bind:value="optionKey" x-text="optionLabel"></option>
						</template>
					</flux:select>
					<flux:input.group x-show="! isSelect">
						<template x-if="prefix !== ''"><flux:input.group.prefix><span x-text="prefix"></span></flux:input.group.prefix></template>
						<flux:input x-model="value" x-bind:type="inputType" placeholder="Enter filter value" maxlength="255" x-on:keydown.enter.prevent="apply()" />
						<template x-if="suffix !== ''"><flux:input.group.suffix><span x-text="suffix"></span></flux:input.group.suffix></template>
					</flux:input.group>
				</flux:field>

				<flux:field x-show="supported && needsValue && needsSecondValue">
					<flux:label>And</flux:label>
					<flux:input.group>
						<template x-if="prefix !== ''"><flux:input.group.prefix><span x-text="prefix"></span></flux:input.group.prefix></template>
						<flux:input x-model="value2" x-bind:type="inputType" placeholder="Enter second value" maxlength="255" x-on:keydown.enter.prevent="apply()" />
						<template x-if="suffix !== ''"><flux:input.group.suffix><span x-text="suffix"></span></flux:input.group.suffix></template>
					</flux:input.group>
				</flux:field>
			</div>

			<flux:separator />

			<div class="flex gap-2">
				<flux:spacer />
				<flux:modal.close>
					<flux:button variant="ghost">Close</flux:button>
				</flux:modal.close>
				<flux:button variant="primary" x-show="supported" x-bind:disabled="! ready" x-on:click="apply()">Apply Filter</flux:button>
			</div>
		</div>
	</flux:modal>

</div>
