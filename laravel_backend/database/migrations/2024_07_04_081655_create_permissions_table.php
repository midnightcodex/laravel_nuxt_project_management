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
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('role_id');
            $table->string('entity_type', 30);
            $table->uuid('entity_id');
            $table->boolean('read_permission')->default(false);
            $table->boolean('write_permission')->default(false);
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->index('entity_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropIndex(['entity_id']);
        });
        Schema::dropIfExists('permissions');
    }
};
