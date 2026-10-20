<?php

namespace Minottam\Datatables;

use Minottam\Datatables\Models\DatatablePreference;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

trait HasDatatable
{
	use WithPagination, WithoutUrlPagination;

	public string $sortBy = '';

	public string $sortDirection = 'asc';

	public int $perPage = 10;

	/**
	 * Applied filters, each: ['column' => string, 'operator' => string, 'value' => ?string].
	 */
	public array $filters = [];

	abstract protected function schema(): Datatable;

	#[Computed]
	public function table(): Datatable
	{
		return $this->schema()
			->sortBy($this->sortBy)
			->sortDirection($this->sortDirection)
			->filters($this->filters);
	}

	protected function applyDefaults(): void
	{
		$this->sortBy = '';
		$this->sortDirection = 'asc';
		$this->filters = [];
		$this->perPage = $this->schema()->getDefaultPerPage();

		foreach ($this->schema()->getColumns() as $column) {
			if ($column->defaultDirection() !== null) {
				$this->sortBy = $column->name();
				$this->sortDirection = $column->defaultDirection();
			}
		}
	}

	public function mountHasDatatable(): void
	{
		$this->applyDefaults();

		$saved = $this->loadPreferences();

		if (
			array_key_exists('sort_by', $saved)
			&& is_string($saved['sort_by'])
			&& ($saved['sort_by'] === '' || $this->isSortable($saved['sort_by']))
		) {
			$this->sortBy = $saved['sort_by'];

			if (in_array($saved['sort_direction'] ?? null, ['asc', 'desc'], true)) {
				$this->sortDirection = $saved['sort_direction'];
			}
		}

		if (in_array($saved['per_page'] ?? null, $this->schema()->getPerPageOptions(), true)) {
			$this->perPage = $saved['per_page'];
		}

		if (is_array($saved['filters'] ?? null)) {
			$this->filters = array_values(array_filter(
				$saved['filters'],
				fn ($filter) => $this->isValidFilter($filter)
			));
		}

		if (is_int($saved['page'] ?? null) && $saved['page'] >= 1) {
			$this->paginators['page'] = $saved['page'];
		}
	}

	protected function filterColumn(mixed $name): ?Column
	{
		if (! is_string($name)) {
			return null;
		}

		foreach ($this->schema()->getColumns() as $column) {
			if ($column->name() === $name && $column->isFilterable() && $column->operators() !== []) {
				return $column;
			}
		}

		return null;
	}

	protected function isValidFilter(mixed $filter): bool
	{
		if (! is_array($filter) || ! is_string($filter['operator'] ?? null)) {
			return false;
		}

		$column = $this->filterColumn($filter['column'] ?? null);

		if ($column === null || ! array_key_exists($filter['operator'], $column->operators())) {
			return false;
		}

		return $column->isValidFilterValue($filter['operator'], $filter['value'] ?? null, $filter['value2'] ?? null);
	}

	public function applyFilter(string $column, string $operator, ?string $value = null, ?string $value2 = null): bool
	{
		$filterColumn = $this->filterColumn($column);

		$filter = [
			'column' => $column,
			'operator' => $operator,
			'value' => ($filterColumn?->operatorNeedsValue($operator) ?? true) ? trim((string) $value) : null,
			'value2' => ($filterColumn?->operatorNeedsSecondValue($operator) ?? false) ? trim((string) $value2) : null,
		];

		if (! $this->isValidFilter($filter)) {
			Flux::toast(text: $this->filterColumn($column)?->filterErrorMessage() ?? __('Invalid filter value.'), variant: 'danger');

			return false;
		}

		if ($this->filterColumn($column)->replacesSameOperator($operator)) {
			$this->filters = array_values(array_filter(
				$this->filters,
				fn ($existing) => ! ($existing['column'] === $column && $existing['operator'] === $operator)
			));
		}

		$this->filters[] = $filter;

		$this->savePreference(['filters' => $this->filters]);

		$this->resetPage();

		return true;
	}

	public function removeFilter(int $index): void
	{
		if (! array_key_exists($index, $this->filters)) {
			return;
		}

		unset($this->filters[$index]);
		$this->filters = array_values($this->filters);

		$this->savePreference(['filters' => $this->filters]);

		$this->resetPage();
	}

	public function clearFilters(): void
	{
		$this->filters = [];

		$this->savePreference(['filters' => []]);

		$this->resetPage();
	}

	protected function preferenceKey(): array
	{
		return ['user_id' => auth()->id(), 'table_key' => $this->getName()];
	}

	protected function loadPreferences(): array
	{
		if (! auth()->check()) {
			return [];
		}

		return DatatablePreference::where($this->preferenceKey())->first()?->preferences ?? [];
	}

	protected function savePreference(array $values): void
	{
		if (! auth()->check()) {
			return;
		}

		$table = (new DatatablePreference)->getTable();
		$key = $this->preferenceKey();
		$now = now();

		DB::statement(
			"INSERT INTO {$table} (id, user_id, table_key, preferences, created_at, updated_at) "
			.'VALUES (?, ?, ?, ?::jsonb, ?, ?) '
			.'ON CONFLICT (user_id, table_key) DO UPDATE SET '
			."preferences = {$table}.preferences || EXCLUDED.preferences, updated_at = EXCLUDED.updated_at",
			[(string) Str::uuid(), $key['user_id'], $key['table_key'], json_encode($values), $now, $now]
		);
	}

	public function resetPreferences(): void
	{
		if (auth()->check()) {
			DatatablePreference::where($this->preferenceKey())->delete();
		}

		$this->applyDefaults();
		$this->paginators['page'] = 1;
	}

	protected function isSortable(string $column): bool
	{
		foreach ($this->schema()->getColumns() as $c) {
			if ($c->name() === $column && $c->isSortable()) {
				return true;
			}
		}

		return false;
	}

	public function sort(string $column): void
	{
		if (! $this->isSortable($column)) {
			return;
		}

		if ($this->sortBy === $column && $this->sortDirection === 'asc') {
			$this->sortDirection = 'desc';
		} elseif ($this->sortBy === $column) {
			$this->sortBy = '';
			$this->sortDirection = 'asc';
		} else {
			$this->sortBy = $column;
			$this->sortDirection = 'asc';
		}

		$this->savePreference(['sort_by' => $this->sortBy, 'sort_direction' => $this->sortDirection]);

		$this->resetPage();
	}

	public function updatedPerPage(): void
	{
		if (in_array($this->perPage, $this->schema()->getPerPageOptions(), true)) {
			$this->savePreference(['per_page' => $this->perPage]);
		}

		$this->resetPage();
	}

	public function updatedPaginators($page, $pageName): void
	{
		if ($pageName === 'page' && is_numeric($page) && (int) $page >= 1) {
			$this->savePreference(['page' => (int) $page]);
		}
	}

	protected function records($query)
	{
		$model = $query->getModel();

		if ($this->filters !== []) {
			$this->table->unfilteredTotal((clone $query)->count());
		}

		if ($this->sortBy !== '' && $this->isSortable($this->sortBy)) {
			$direction = $this->sortDirection === 'desc' ? 'desc' : 'asc';

			$query->orderBy($this->sortBy, $direction);
		}

		foreach ($this->filters as $filter) {
			$this->filterColumn($filter['column'])?->applyFilter($query, $filter['operator'], $filter['value'] ?? null, $filter['value2'] ?? null);
		}

		$query->orderBy($model->qualifyColumn('updated_at'), 'desc')
			->orderBy($model->getQualifiedKeyName());

		$definition = $this->schema();
		$perPage = in_array($this->perPage, $definition->getPerPageOptions(), true) ? $this->perPage : $definition->getDefaultPerPage();

		$paginator = $query->paginate($perPage);

		if ($paginator->currentPage() > $paginator->lastPage()) {
			$this->paginators['page'] = $paginator->lastPage();

			$paginator = $query->paginate($perPage, ['*'], 'page', $paginator->lastPage());
		}

		return $paginator;
	}
}
