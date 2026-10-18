<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damages', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 30)->unique();
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_location_id')->constrained()->restrictOnDelete();
            $table->foreignId('reported_by')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('damage_type', 40);
            $table->string('status', 30)->default('dilaporkan');
            $table->date('reported_at');
            $table->text('description')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('handling_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damages');
    }
};
