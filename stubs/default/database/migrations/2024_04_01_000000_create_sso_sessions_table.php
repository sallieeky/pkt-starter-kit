<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sso_sessions', function (Blueprint $table) {
            $table->string('SESSION_ID')->primary()->unique();

            $table->string('USER_ID')->nullable();
            $table->string('TOKEN')->nullable();
            $table->string('USER_ALIASES')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sso_sessions');
    }
};
