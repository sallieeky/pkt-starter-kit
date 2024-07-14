<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
        DB::statement('DROP VIEW IF EXISTS table_name');

        /**
         * ============================================
         * Create the view with the given query.
         * ============================================
         */
        DB::statement("
            CREATE VIEW table_name AS
            (
                select * from related_table
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /**
         * ============================================
         * Drop the view if it already exists.
         * ============================================
         */
        DB::statement('DROP VIEW IF EXISTS table_name');
    }
};
