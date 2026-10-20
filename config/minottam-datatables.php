<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Preferences table
	|--------------------------------------------------------------------------
	|
	| The table that stores each user's saved sort, page length, page and
	| filters. Used by the DatatablePreference model and the migration.
	|
	*/

	'table' => 'datatable_preferences',

	/*
	|--------------------------------------------------------------------------
	| User key type
	|--------------------------------------------------------------------------
	|
	| The type of the primary key on your users table: 'uuid' or 'id'.
	| Read by the migration when it creates the user_id column.
	|
	*/

	'user_key_type' => 'uuid',

];
