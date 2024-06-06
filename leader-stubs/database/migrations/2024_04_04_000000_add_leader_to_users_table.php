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
        Schema::table('users', function (Blueprint $table) {
            $table->string('hierarchy_code')->nullable()->after('is_active');
            $table->string('position_id')->nullable()->after('hierarchy_code');
            $table->string('position')->nullable()->after('position_id');
            $table->string('work_unit_id')->nullable()->after('position');
            $table->string('work_unit')->nullable()->after('work_unit_id');
            $table->string('user_flag')->nullable()->after('work_unit');
            $table->string('user_alias')->nullable()->after('user_flag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('hierarchy_code');
            $table->dropColumn('position_id');
            $table->dropColumn('position');
            $table->dropColumn('work_unit_id');
            $table->dropColumn('work_unit');
            $table->dropColumn('user_flag');
            $table->dropColumn('user_alias');
        });
    }
};
