<?php

use Illuminate\Database\Migrations\Migration;
use Staudenmeir\LaravelMigrationViews\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * ============================================
         * Drop the view if it already exists.
         * ============================================
         */
        Schema::dropViewIfExists('table_name');

        /**
         * ============================================
         * Create the view with the given query.
         * ============================================
         */
        $query = \App\Models\ModelName::query();
        Schema::createView('table_name', $query);
    }
};
