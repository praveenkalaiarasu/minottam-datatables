<?php

namespace Minottam\Datatables;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class DatatablesServiceProvider extends ServiceProvider
{
	public function boot(): void
	{
		Blade::anonymousComponentPath(__DIR__.'/../resources/views/components');

		$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
	}
}
