<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('name');
            $table->string('gstin', 15)->nullable()->after('business_name');
            $table->text('address')->nullable()->after('gstin');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('state_code', 2)->nullable()->after('state');
            $table->string('pincode', 6)->nullable()->after('state_code');
            $table->string('phone', 15)->nullable()->after('pincode');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'gstin',
                'address',
                'city',
                'state',
                'state_code',
                'pincode',
                'phone',
            ]);
        });
    }
};
