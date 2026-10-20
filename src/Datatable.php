<?php

namespace Minottam\Datatables;

class Datatable
{
	protected array $columns = [];

	protected string $sortBy = '';

	protected string $sortDirection = 'asc';

	protected array $filters = [];

	protected ?int $unfilteredTotal = null;

	protected int $defaultPerPage = 10;

	protected array $perPageOptions = [10, 25, 50, 100];

	public static function make(): static
	{
		return new static;
	}

	public function columns(array $columns): static
	{
		$this->columns = $columns;

		return $this;
	}

	public function getColumns(): array
	{
		return $this->columns;
	}

	public function sortBy(string $sortBy): static
	{
		$this->sortBy = $sortBy;

		return $this;
	}

	public function getSortBy(): string
	{
		return $this->sortBy;
	}

	public function sortDirection(string $sortDirection): static
	{
		$this->sortDirection = $sortDirection;

		return $this;
	}

	public function getSortDirection(): string
	{
		return $this->sortDirection;
	}

	public function filters(array $filters): static
	{
		$this->filters = $filters;

		return $this;
	}

	public function getFilters(): array
	{
		return $this->filters;
	}

	public function unfilteredTotal(?int $unfilteredTotal): static
	{
		$this->unfilteredTotal = $unfilteredTotal;

		return $this;
	}

	public function getUnfilteredTotal(): ?int
	{
		return $this->unfilteredTotal;
	}

	public function defaultPerPage(int $defaultPerPage): static
	{
		$this->defaultPerPage = $defaultPerPage;

		return $this;
	}

	public function getDefaultPerPage(): int
	{
		return $this->defaultPerPage;
	}

	public function perPageOptions(array $perPageOptions): static
	{
		$this->perPageOptions = $perPageOptions;

		return $this;
	}

	public function getPerPageOptions(): array
	{
		return $this->perPageOptions;
	}
}
