<?php

namespace Minottam\Datatables\Columns;

use Minottam\Datatables\Column;
use Illuminate\Database\Eloquent\Builder;

class TextColumn extends Column
{
	public function operators(): array
	{
		return [
			'contains' => 'Contains',
			'equals' => 'Equals',
			'starts_with' => 'Starts with',
			'ends_with' => 'Ends with',
			'is_empty' => 'Is empty',
		];
	}

	public function inputType(): ?string
	{
		return 'text';
	}

	public function applyFilter(Builder $query, string $operator, mixed $value = null, mixed $value2 = null): void
	{
		$column = $query->getModel()->qualifyColumn($this->name());
		$text = $this->escapeLike((string) $value);

		match ($operator) {
			'contains' => $query->where($column, 'ilike', "%{$text}%"),
			'equals' => $query->where($column, 'ilike', $text),
			'starts_with' => $query->where($column, 'ilike', "{$text}%"),
			'ends_with' => $query->where($column, 'ilike', "%{$text}"),
			'is_empty' => $query->where(fn (Builder $q) => $q->whereNull($column)->orWhere($column, '')),
			default => throw new \InvalidArgumentException("Column [{$this->name()}]: unsupported filter operator '{$operator}'."),
		};
	}

	protected function escapeLike(string $value): string
	{
		return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
	}
}
