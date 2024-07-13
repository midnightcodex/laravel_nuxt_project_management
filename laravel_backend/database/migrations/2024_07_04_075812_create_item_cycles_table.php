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
        Schema::create('item_cycles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('item_id');
            $table->enum('status', ['created', 'reviewed', 'accepted', 'rejected', 'reassigned', 'working', 'status update', 'won', 'lost', 'closed', 'reopened', 'assigned', 'completed']);
            $table->uuid('updated_by');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('item_cycles', function (Blueprint $table) {
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_cycles', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropForeign(['updated_by']);
            $table->dropIndex(['item_id']);
        });
        Schema::dropIfExists('item_cycles');
    }
};
