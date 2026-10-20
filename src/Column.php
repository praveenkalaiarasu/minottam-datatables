<?php

namespace Minottam\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Column
{
	protected bool $sortable = false;

	protected ?string $defaultDirection = null;

	protected bool $filterable = false;

	protected ?string $label = null;

	protected ?string $width = null;

	public function __construct(protected string $name)
	{
		//
	}

	public static function make(string $name): static
	{
		return new static($name);
	}

	public function sortable(?string $default = null): static
	{
		if ($default !== null && ! in_array($default, ['asc', 'desc'])) {
			throw new \InvalidArgumentException("Column [{$this->name}]: sortable() accepts 'asc' or 'desc', got '{$default}'.");
		}

		$this->sortable = true;
		$this->defaultDirection = $default;

		return $this;
	}

	public function filterable(): static
	{
		$this->filterable = true;

		return $this;
	}

	public function label(string $label): static
	{
		$this->label = $label;

		return $this;
	}

	public function width(string $width): static
	{
		$this->width = $width;

		return $this;
	}

	public function getWidth(): ?string
	{
		return $this->width;
	}

	public function name(): string
	{
		return $this->name;
	}

	public function getLabel(): string
	{
		return $this->label ?? Str::headline($this->name);
	}

	public function isIndex(): bool
	{
		return false;
	}

	public function isAction(): bool
	{
		return false;
	}

	public function isSortable(): bool
	{
		return $this->sortable;
	}

	public function isFilterable(): bool
	{
		return $this->filterable;
	}

	public function defaultDirection(): ?string
	{
		return $this->defaultDirection;
	}

	/**
	 * Filter conditions this column offers, as value => label.
	 *
	 * @return array<string, string>
	 */
	public function operators(): array
	{
		return [];
	}

	public function inputType(): ?string
	{
		return null;
	}

	/**
	 * Allowed values as value => label, for columns that pick from a fixed list.
	 *
	 * @return array<string, string>
	 */
	public function getOptions(): array
	{
		return [];
	}

	public function getPrefix(): ?string
	{
		return null;
	}

	public function getSuffix(): ?string
	{
		return null;
	}

	/**
	 * Display form of a cell value; the stored and queried value is never changed.
	 */
	public function formatValue(mixed $value): mixed
	{
		return $value;
	}

	/**
	 * Display form of a submitted filter value, as shown in the filter chips.
	 */
	public function formatFilterValue(string $value): string
	{
		return $value;
	}

	public function applyFilter(Builder $query, string $operator, mixed $value = null, mixed $value2 = null): void
	{
		//
	}

	/**
	 * Whether applying this operator again replaces an earlier filter on this column with the same operator.
	 */
	public function replacesSameOperator(string $operator): bool
	{
		return false;
	}

	public function filterErrorMessage(): string
	{
		return __('Invalid filter value.');
	}

	/**
	 * Whether the operator takes a value; false for operators like "is empty".
	 */
	public function operatorNeedsValue(string $operator): bool
	{
		return $operator !== 'is_empty';
	}

	/**
	 * Whether the operator takes a second value, as in a range.
	 */
	public function operatorNeedsSecondValue(string $operator): bool
	{
		return $operator === 'between';
	}

	/**
	 * Whether the submitted value(s) are acceptable for the given operator.
	 */
	public function isValidFilterValue(string $operator, mixed $value, mixed $value2 = null): bool
	{
		if (! $this->operatorNeedsValue($operator)) {
			return true;
		}

		return is_string($value) && $value !== '' && mb_strlen($value) <= 255;
	}
}
