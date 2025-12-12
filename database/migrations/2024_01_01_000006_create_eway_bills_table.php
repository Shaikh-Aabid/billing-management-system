<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('eway_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->string('eway_bill_number', 50)->unique();
            $table->enum('transport_mode', ['Road', 'Rail', 'Air', 'Ship'])->default('Road');
            $table->string('vehicle_number', 20);
            $table->string('vehicle_type', 50)->nullable();
            $table->string('transporter_id', 15)->nullable();
            $table->string('transporter_name')->nullable();
            $table->integer('distance');
            $table->text('from_address');
            $table->string('from_pincode', 6)->nullable();
            $table->text('to_address');
            $table->string('to_pincode', 6)->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->enum('status', ['active', 'cancelled', 'expired'])->default('active');
            $table->timestamps();

            $table->index('eway_bill_number');
            $table->index('vehicle_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eway_bills');
    }
};
