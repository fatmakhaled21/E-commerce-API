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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_trendy')->default(false);
            $table->boolean('ia_available')->default(true);
            $table->double('price',8,2);
            $table->integer('amount');
            $table->double('discount',8,2)->nullable();
            $table->string('image');
            $table->timestamps();

            $table->foreignId('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->foreignId('brand_id')->references('id')->on('brands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
