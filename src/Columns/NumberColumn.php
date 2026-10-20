<?php

namespace Minottam\Datatables\Columns;

use Minottam\Datatables\Column;
use Illuminate\Database\Eloquent\Builder;

abstract class NumberColumn extends Column
{
	protected ?string $prefix = null;

	protected ?string $suffix = null;

	public function prefix(string $prefix): static
	{
		$this->prefix = $prefix;

		return $this;
	}

	public function suffix(string $suffix): static
	{
		$this->suffix = $suffix;

		return $this;
	}

	public function getPrefix(): ?string
	{
		return $this->prefix;
	}

	public function getSuffix(): ?string
	{
		return $this->suffix;
	}

	public function formatValue(mixed $value): mixed
	{
		if ($value === null || $value === '') {
			return $value;
		}

		return trim(($this->prefix ?? '').$value.($this->suffix ?? ''));
	}

	abstract protected function isValidNumber(string $value): bool;

	abstract protected function castNumber(string $value): int|float;

	public function operators(): array
	{
		return [
			'equals' => 'Equals',
			'not_equals' => 'Not equals',
			'greater_than' => 'Greater than',
			'greater_than_or_equal' => 'Greater than or equal',
			'less_than' => 'Less than',
			'less_than_or_equal' => 'Less than or equal',
			'between' => 'Between',
			'is_empty' => 'Is empty',
		];
	}

	public function inputType(): ?string
	{
		return 'number';
	}

	public function replacesSameOperator(string $operator): bool
	{
		return $operator !== 'not_equals';
	}

	public function isValidFilterValue(string $operator, mixed $value, mixed $value2 = null): bool
	{
		if (! $this->operatorNeedsValue($operator)) {
			return true;
		}

		if (! is_string($value) || ! $this->isValidNumber($value)) {
			return false;
		}

		return ! $this->operatorNeedsSecondValue($operator) || (is_string($value2) && $this->isValidNumber($value2));
	}

	public function applyFilter(Builder $query, string $operator, mixed $value = null, mixed $value2 = null): void
	{
		$column = $query->getModel()->qualifyColumn($this->name());

		match ($operator) {
			'equals' => $query->where($column, '=', $this->castNumber((string) $value)),
			'not_equals' => $query->where($column, '<>', $this->castNumber((string) $value)),
			'greater_than' => $query->where($column, '>', $this->castNumber((string) $value)),
			'greater_than_or_equal' => $query->where($column, '>=', $this->castNumber((string) $value)),
			'less_than' => $query->where($column, '<', $this->castNumber((string) $value)),
			'less_than_or_equal' => $query->where($column, '<=', $this->castNumber((string) $value)),
			'between' => $query->whereBetween($column, [$this->castNumber((string) $value), $this->castNumber((string) $value2)]),
			'is_empty' => $query->whereNull($column),
			default => throw new \InvalidArgumentException("Column [{$this->name()}]: unsupported filter operator '{$operator}'."),
		};
	}
}
