<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // todos table
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['complete', 'incomplete'])->default('incomplete');

            // 🟡 Vehicle-level delay fields
            $table->boolean('is_delayed')->default(false);
            $table->text('delay_reason')->nullable();
            $table->timestamp('delayed_until')->nullable();

            $table->timestamps();
        });


        // todo_lists table
        Schema::create('todo_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_id')->constrained()->onDelete('cascade');
            $table->enum('todo_list_type', ['core', 'custom'])->default('core');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_delayed')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // todo_list_items table
        Schema::create('todo_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_list_id')->constrained()->onDelete('cascade');
            $table->string('title'); // ✅ Fixed: don't make this a foreignId
            $table->text('description')->nullable();
            $table->string('item_type');
            $table->integer('display_order')->default(0); // ✅ fixed type + removed duplicate
            $table->tinyInteger('is_required')->default(0);
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // assigned_todo_lists table
        Schema::create('assigned_todo_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('todo_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->enum('assigned_as', ['manager', 'staff']);
            $table->timestamps();
        });

        // todo_list_results table
        Schema::create('todo_list_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_id')->constrained()->onDelete('cascade');
            $table->foreignId('todo_list_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'delayed'])->default('pending'); // ✅ fixed
            $table->text('delay_reason')->nullable();
            $table->timestamp('delayed_until')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // todo_list_item_results table
        Schema::create('todo_list_items_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('todo_list_result_id')->constrained()->onDelete('cascade');
            $table->foreignId('todo_list_item_id')->constrained()->onDelete('cascade');
            $table->string('value')->nullable();
            $table->string('size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_list_items_results');
        Schema::dropIfExists('todo_list_results');
        Schema::dropIfExists('assigned_todo_lists');
        Schema::dropIfExists('todo_list_items');
        Schema::dropIfExists('todo_lists');
        Schema::dropIfExists('todos');
    }
};
