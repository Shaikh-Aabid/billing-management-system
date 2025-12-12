<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('hsn_code', 8);
            $table->string('unit', 20)->default('Nos');
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('gst_rate', 5, 2)->default(18);
            $table->integer('stock_quantity')->nullable();
            $table->integer('min_stock_level')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'name']);
            $table->index('hsn_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
