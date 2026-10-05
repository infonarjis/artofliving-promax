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
            $table->integer('staff_id')
                ->nullable()
                ->after('franchise_comm_amt');

            $table->decimal('staff_comm_per', 5, 2)
                ->default(0.00)
                ->comment('Staff commission percentage')
                ->after('staff_id');

            $table->decimal('staff_comm_amt', 10, 2)
                ->default(0.00)
                ->comment('Staff commission amount')
                ->after('staff_comm_per');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'staff_id',
                'staff_comm_per',
                'staff_comm_amt',
            ]);
        });
    }
};
