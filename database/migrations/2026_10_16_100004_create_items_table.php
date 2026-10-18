<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 40)->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_location_id')->constrained()->restrictOnDelete();
            $table->string('unit', 20)->default('pcs');
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('min_stock')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
