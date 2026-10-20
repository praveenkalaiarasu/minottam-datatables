<?php

namespace Minottam\Datatables\Columns;

use Minottam\Datatables\Column;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SelectColumn extends Column
{
	/**
	 * Allowed stored values, as value => label.
	 *
	 * @var array<string, string>
	 */
	protected array $options = [];

	public function options(array $options): static
	{
		$this->options = $options;

		return $this;
	}

	/**
	 * @param  class-string<BackedEnum>  $enum
	 */
	public function enum(string $enum): static
	{
		$options = [];

		foreach ($enum::cases() as $case) {
			$options[(string) $case->value] = Str::headline((string) $case->value);
		}

		return $this->options($options);
	}

	public function getOptions(): array
	{
		return $this->options;
	}

	public function operators(): array
	{
		return [
			'is' => 'Is',
			'is_not' => 'Is not',
		];
	}

	public function inputType(): ?string
	{
		return 'select';
	}

	public function replacesSameOperator(string $operator): bool
	{
		return $operator === 'is';
	}

	public function filterErrorMessage(): string
	{
		return __('Select a valid option.');
	}

	public function isValidFilterValue(string $operator, mixed $value, mixed $value2 = null): bool
	{
		return is_string($value) && array_key_exists($value, $this->options);
	}

	public function formatValue(mixed $value): mixed
	{
		if ($value === null) {
			return null;
		}

		if ($value instanceof BackedEnum) {
			$value = $value->value;
		}

		return $this->options[(string) $value] ?? $value;
	}

	public function applyFilter(Builder $query, string $operator, mixed $value = null, mixed $value2 = null): void
	{
		$column = $query->getModel()->qualifyColumn($this->name());

		match ($operator) {
			'is' => $query->where($column, (string) $value),
			'is_not' => $query->where(fn (Builder $q) => $q->where($column, '<>', (string) $value)->orWhereNull($column)),
			default => throw new \InvalidArgumentException("Column [{$this->name()}]: unsupported filter operator '{$operator}'."),
		};
	}
}
