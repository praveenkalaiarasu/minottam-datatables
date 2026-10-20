<?php

namespace Minottam\Datatables\Columns;

use Minottam\Datatables\Column;
use Illuminate\Database\Eloquent\Builder;

abstract class TemporalColumn extends Column
{
	/**
	 * Return the value in its stored format when it is valid for this column, or null when it is not.
	 */
	abstract protected function parse(string $value): ?string;

	protected ?string $format = null;

	/**
	 * Set the PHP date format used to display values in cells and filter chips.
	 */
	public function format(string $format): static
	{
		$this->format = $format;

		return $this;
	}

	public function operators(): array
	{
		return [
			'on' => 'On',
			'before' => 'Before',
			'after' => 'After',
			'between' => 'Between',
			'is_empty' => 'Is empty',
		];
	}

	public function replacesSameOperator(string $operator): bool
	{
		return true;
	}

	public function isValidFilterValue(string $operator, mixed $value, mixed $value2 = null): bool
	{
		if (! $this->operatorNeedsValue($operator)) {
			return true;
		}

		if (! is_string($value) || $this->parse($value) === null) {
			return false;
		}

		return ! $this->operatorNeedsSecondValue($operator) || (is_string($value2) && $this->parse($value2) !== null);
	}

	public function applyFilter(Builder $query, string $operator, mixed $value = null, mixed $value2 = null): void
	{
		$column = $query->getModel()->qualifyColumn($this->name());

		match ($operator) {
			'on' => $query->where($column, '=', $this->parse((string) $value)),
			'before' => $query->where($column, '<', $this->parse((string) $value)),
			'after' => $query->where($column, '>', $this->parse((string) $value)),
			'between' => $query->whereBetween($column, [$this->parse((string) $value), $this->parse((string) $value2)]),
			'is_empty' => $query->whereNull($column),
			default => throw new \InvalidArgumentException("Column [{$this->name()}]: unsupported filter operator '{$operator}'."),
		};
	}
}
