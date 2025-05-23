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
        Schema::create('inspection_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_type_id')->constrained('inspection_types')->cascadeOnDelete();
            $table->string('uuid', 16)->unique();

            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('display_order');
            $table->boolean('is_required')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_checklists');
    }
};
