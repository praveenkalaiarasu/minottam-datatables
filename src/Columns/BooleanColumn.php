<?php

namespace Minottam\Datatables\Columns;

use Minottam\Datatables\Column;
use Illuminate\Database\Eloquent\Builder;

class BooleanColumn extends Column
{
	protected string $trueLabel = 'Yes';

	protected string $falseLabel = 'No';

	public function labels(string $true, string $false): static
	{
		$this->trueLabel = $true;
		$this->falseLabel = $false;

		return $this;
	}

	public function operators(): array
	{
		return [
			'is_true' => "Is {$this->trueLabel}",
			'is_false' => "Is {$this->falseLabel}",
		];
	}

	public function formatValue(mixed $value): mixed
	{
		if ($value === null) {
			return null;
		}

		return $value ? $this->trueLabel : $this->falseLabel;
	}

	public function operatorNeedsValue(string $operator): bool
	{
		return false;
	}

	public function replacesSameOperator(string $operator): bool
	{
		return true;
	}

	public function applyFilter(Builder $query, string $operator, mixed $value = null, mixed $value2 = null): void
	{
		$column = $query->getModel()->qualifyColumn($this->name());

		match ($operator) {
			'is_true' => $query->where($column, true),
			'is_false' => $query->where($column, false),
			default => throw new \InvalidArgumentException("Column [{$this->name()}]: unsupported filter operator '{$operator}'."),
		};
	}
}
