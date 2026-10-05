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
        Schema::create('staff_kpi_assign_members', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id')
                ->index();
            $table->integer('member_id')
                ->default(0)
                ->index();
            $table->string('matri_id')
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
        Schema::dropIfExists('staff_kpi_assign_members');
    }
};
