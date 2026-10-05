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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('purchase_token', 255)->nullable()->default('')->after('member_id');
            $table->boolean('auto_renewing')->default(true)->after('plan_expiry_date');
            $table->string('payment_received_from', 100)->nullable()->after('payment_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_token',
                'auto_renewing',
                'payment_received_from',
            ]);
        });
    }
};
