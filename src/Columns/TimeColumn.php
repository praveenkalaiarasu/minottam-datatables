<?php

namespace Minottam\Datatables\Columns;

class TimeColumn extends TemporalColumn
{
	public function operators(): array
	{
		return ['on' => 'At'] + parent::operators();
	}

	public function inputType(): ?string
	{
		return 'time';
	}

	public function filterErrorMessage(): string
	{
		return __('Enter a valid time.');
	}

	public function formatValue(mixed $value): mixed
	{
		if ($this->format === null || ! is_string($value)) {
			return $value;
		}

		return $this->formatFilterValue($value);
	}

	public function formatFilterValue(string $value): string
	{
		$parsed = $this->parse($value);

		if ($this->format === null || $parsed === null) {
			return $value;
		}

		return \DateTimeImmutable::createFromFormat('!H:i:s', $parsed)->format($this->format);
	}

	protected function parse(string $value): ?string
	{
		foreach (['!H:i:s', '!H:i'] as $format) {
			$time = \DateTimeImmutable::createFromFormat($format, $value);

			if ($time !== false && $time->format($format === '!H:i' ? 'H:i' : 'H:i:s') === $value) {
				return $time->format('H:i:s');
			}
		}

		return null;
	}
}
