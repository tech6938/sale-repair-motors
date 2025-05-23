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
        Schema::create('checklist_item_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_checklist_result_id')->constrained('inspection_checklist_results')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->cascadeOnDelete();
            $table->string('uuid', 16)->unique();

            $table->json('value');
            $table->float('size')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_item_results');
    }
};
