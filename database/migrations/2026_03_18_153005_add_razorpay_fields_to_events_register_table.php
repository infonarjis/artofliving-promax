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
        Schema::table('events_register', function (Blueprint $table) {

            $table->string('transaction_order_id')->nullable()->after('payment_mode');
            $table->string('transaction_id')->nullable()->after('transaction_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_register', function (Blueprint $table) {

            $table->dropColumn([
                'transaction_order_id',
                'transaction_id'
            ]);

        });
    }
};
