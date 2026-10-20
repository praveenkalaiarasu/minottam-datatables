<?php

namespace Minottam\Datatables\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DatatablePreference extends Model
{
	use HasUuids;

	protected $fillable = ['user_id', 'table_key', 'preferences'];

	public function getTable(): string
	{
		return config('minottam-datatables.table', 'datatable_preferences');
	}

	protected function casts(): array
	{
		return [
			'preferences' => 'array',
		];
	}
}
