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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('original_name');
            $table->string('storage_name');
            $table->string('path');
            $table->string('type');
            $table->bigInteger('size');
            $table->string('extension');
            $table->string('mime_type');
            $table->timestamps();
            $table->softDeletes();
            $table->createdUpdatedBy();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
