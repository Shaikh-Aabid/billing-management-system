<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('gstin', 15)->nullable();
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('state_code', 2)->nullable();
            $table->string('pincode', 6)->nullable();
            $table->string('contact_number', 15)->nullable();
            $table->string('email')->nullable();
            $table->enum('party_type', ['customer', 'supplier'])->default('customer');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'name']);
            $table->index('gstin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
