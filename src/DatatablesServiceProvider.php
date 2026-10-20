<?php

namespace Minottam\Datatables;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class DatatablesServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->mergeConfigFrom(__DIR__.'/../config/minottam-datatables.php', 'minottam-datatables');
	}

	public function boot(): void
	{
		Blade::anonymousComponentPath(__DIR__.'/../resources/views/components');

		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__.'/../config/minottam-datatables.php' => config_path('minottam-datatables.php'),
			], 'datatables-config');

			$this->publishes([
				__DIR__.'/../database/migrations/0001_01_03_100001_create_datatable_preferences_table.php' => database_path('migrations/0001_01_03_100001_create_datatable_preferences_table.php'),
			], 'datatables-migrations');
		}
	}
}
