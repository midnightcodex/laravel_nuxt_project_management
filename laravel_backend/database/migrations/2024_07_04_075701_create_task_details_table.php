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
        Schema::create('task_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('item_id');
            $table->date('task_due_date')->nullable();
            $table->enum('task_priority', ['low', 'medium', 'high']);
            $table->string('task_type')->nullable();
            // -- other task-specific columns
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_details', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('task_details');
    }
};
