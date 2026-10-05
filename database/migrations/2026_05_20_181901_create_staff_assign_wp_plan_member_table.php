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
        Schema::create('staff_assign_wp_plan_member', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id')
                ->index();
            $table->string('matri_id')
                ->nullable()
                ->index();
            $table->integer('member_id')
                ->default(0)
                ->index();
            $table->integer('payment_id')
                ->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_assign_wp_plan_member');
    }
};
