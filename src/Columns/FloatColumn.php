<?php

namespace Minottam\Datatables\Columns;

class FloatColumn extends NumberColumn
{
	public function filterErrorMessage(): string
	{
		return __('Enter a valid number, max. 10 decimals.');
	}

	protected function isValidNumber(string $value): bool
	{
		return preg_match('/^-?\d{1,15}(\.\d{1,10})?$/', $value) === 1;
	}

	protected function castNumber(string $value): float
	{
		return (float) $value;
	}
}
