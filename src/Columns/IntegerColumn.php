<?php

namespace Minottam\Datatables\Columns;

class IntegerColumn extends NumberColumn
{
	public function filterErrorMessage(): string
	{
		return __('Enter a whole number.');
	}

	protected function isValidNumber(string $value): bool
	{
		return preg_match('/^-?\d{1,18}$/', $value) === 1;
	}

	protected function castNumber(string $value): int
	{
		return (int) $value;
	}
}
