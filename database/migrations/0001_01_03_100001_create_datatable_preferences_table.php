<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create(config('minottam-datatables.table', 'datatable_preferences'), function (Blueprint $table) {
			$table->uuid('id')->primary();

			if (config('minottam-datatables.user_key_type', 'uuid') === 'id') {
				$table->foreignId('user_id')->constrained()->cascadeOnDelete();
			} else {
				$table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
			}

			$table->string('table_key');
			$table->jsonb('preferences');
			$table->timestamps();

			$table->unique(['user_id', 'table_key']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists(config('minottam-datatables.table', 'datatable_preferences'));
	}
};
