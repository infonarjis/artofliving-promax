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
        Schema::create('staff_kpis', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_kpi_assign_member_id')
                ->default(0)
                ->index();
            $table->integer('staff_id')
                ->index();
            $table->string('matri_id')
                ->nullable()
                ->index();
            $table->integer('member_id')
                ->default(0)
                ->index();
            $table->integer('payment_id')
                ->default(0)
                ->index();
            $table->integer('plan_id')
                ->index();
            $table->string('plan_name')
                ->nullable();
            $table->double('plan_amount')
                ->default(0);
            $table->double('plan_offer_amount')
                ->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_kpis');
    }
};
