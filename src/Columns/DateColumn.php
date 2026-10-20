<?php

namespace Minottam\Datatables\Columns;

class DateColumn extends TemporalColumn
{
	public function inputType(): ?string
	{
		return 'date';
	}

	public function filterErrorMessage(): string
	{
		return __('Enter a valid date.');
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
		if ($this->format === null || $this->parse($value) === null) {
			return $value;
		}

		return \DateTimeImmutable::createFromFormat('!Y-m-d', $value)->format($this->format);
	}

	protected function parse(string $value): ?string
	{
		$date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

		return $date !== false && $date->format('Y-m-d') === $value ? $value : null;
	}
}
