<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Staudenmeir\LaravelMigrationViews\Facades\Schema as FacadesSchema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $query = \App\Models\Views\ModelName::query();
        FacadesSchema::createView('table_name', $query);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        FacadesSchema::dropViewIfExists('table_name');
    }
};
