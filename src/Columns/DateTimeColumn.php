<?php

namespace Minottam\Datatables\Columns;

use Illuminate\Database\Eloquent\Builder;

class DateTimeColumn extends TemporalColumn
{
	public function operators(): array
	{
		return ['on' => 'At'] + parent::operators();
	}

	public function inputType(): ?string
	{
		return 'datetime-local';
	}

	public function filterErrorMessage(): string
	{
		return __('Enter a valid date and time.');
	}

	public function applyFilter(Builder $query, string $operator, mixed $value = null, mixed $value2 = null): void
	{
		$column = $query->getModel()->qualifyColumn($this->name());

		match ($operator) {
			'on' => $query->whereBetween($column, [$this->parse((string) $value), $this->endOfInput((string) $value)]),
			'between' => $query->whereBetween($column, [$this->parse((string) $value), $this->endOfInput((string) $value2)]),
			default => parent::applyFilter($query, $operator, $value, $value2),
		};
	}

	public function formatValue(mixed $value): mixed
	{
		if ($this->format === null || $value === null || $value === '') {
			return $value;
		}

		if ($value instanceof \DateTimeInterface) {
			return $value->format($this->format);
		}

		return is_string($value) ? $this->formatFilterValue($value) : $value;
	}

	public function formatFilterValue(string $value): string
	{
		$parsed = $this->parse($value);

		if ($this->format === null || $parsed === null) {
			return $value;
		}

		return \DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $parsed)->format($this->format);
	}

	protected function parse(string $value): ?string
	{
		foreach (['Y-m-d\TH:i:s', 'Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
			$dateTime = \DateTimeImmutable::createFromFormat('!'.$format, $value);

			if ($dateTime !== false && $dateTime->format($format) === $value) {
				return $dateTime->format('Y-m-d H:i:s');
			}
		}

		return null;
	}

	/**
	 * Without seconds in the input, the value stands for the whole minute, so it ends at :59.
	 */
	protected function endOfInput(string $value): ?string
	{
		$parsed = $this->parse($value);

		if ($parsed === null || preg_match('/\d{2}:\d{2}:\d{2}$/', $value) === 1) {
			return $parsed;
		}

		return substr($parsed, 0, 16).':59';
	}
}
