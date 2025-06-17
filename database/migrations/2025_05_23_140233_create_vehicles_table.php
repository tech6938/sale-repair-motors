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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('uuid', 16)->unique();

            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('fuel_type');
            $table->string('color');
            $table->string('license_plate', 20);
            $table->float('milage');
            $table->string('registration');

            $table->boolean('mechanical_fault')->nullable();
            $table->boolean('bodywork_damage')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
