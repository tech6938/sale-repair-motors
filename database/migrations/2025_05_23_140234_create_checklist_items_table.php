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
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_checklist_id')->constrained('inspection_checklists')->cascadeOnDelete();
            $table->string('uuid', 16)->unique();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('item_type');
            $table->integer('display_order');
            $table->boolean('is_required')->default(true);
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
