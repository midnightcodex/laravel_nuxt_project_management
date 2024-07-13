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
        Schema::create('lead_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('item_id');
            $table->string('lead_source')->nullable();
            $table->decimal('lead_value', 10, 2)->nullable();
            $table->date('potential_close_date')->nullable();
            // -- other lead-specific columns
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_details', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('lead_details');
    }
};
