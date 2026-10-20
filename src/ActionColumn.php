<?php

namespace Minottam\Datatables;

class ActionColumn extends Column
{
	public function __construct(string $name = 'actions')
	{
		parent::__construct($name);

		$this->label('Actions');
	}

	public static function make(string $name = 'actions'): static
	{
		return new static($name);
	}

	public function buttons(int $count): static
	{
		$count = max(1, $count);
		$content = 1.5 + ($count * 1.5) + (($count - 1) * 0.25);

		return $this->width(max(5, $content) . 'rem');
	}

	public function isAction(): bool
	{
		return true;
	}
}
