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
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->string('in_app_purchase_android_id')->nullable()->after('plan_amount');
            $table->decimal('in_app_purchase_android_amount', 10, 2)->nullable()->after('in_app_purchase_android_id');
            $table->string('in_app_purchase_ios_id')->nullable()->after('in_app_purchase_android_amount');
            $table->decimal('in_app_purchase_ios_amount', 10, 2)->nullable()->after('in_app_purchase_ios_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn([
                'in_app_purchase_android_id',
                'in_app_purchase_android_amount',
                'in_app_purchase_ios_id',
                'in_app_purchase_ios_amount',
            ]);
        });
    }
};
