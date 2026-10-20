<?php

namespace Minottam\Datatables;

class IndexColumn extends Column
{
	public function __construct()
	{
		parent::__construct('#');

		$this->label('#');
	}

	public static function make(string $name = '#'): static
	{
		return new static;
	}

	public function isIndex(): bool
	{
		return true;
	}
}
