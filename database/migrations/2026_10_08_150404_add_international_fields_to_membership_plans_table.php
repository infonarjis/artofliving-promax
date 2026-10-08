<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {

            $table->char('international_currency_code', 3)
                ->nullable()
                ->after('plan_amount');

            $table->decimal('international_plan_amount', 10, 2)
                ->nullable()
                ->after('international_currency_code');

            $table->decimal('international_in_app_purchase_android_amount', 10, 2)
                ->nullable()
                ->after('in_app_purchase_android_amount');

            $table->decimal('international_in_app_purchase_ios_amount', 10, 2)
                ->nullable()
                ->after('in_app_purchase_ios_amount');
        });
    }

    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {

            $table->dropColumn([
                'international_currency_code',
                'international_plan_amount',
                'international_in_app_purchase_android_amount',
                'international_in_app_purchase_ios_amount',
            ]);
        });
    }
};