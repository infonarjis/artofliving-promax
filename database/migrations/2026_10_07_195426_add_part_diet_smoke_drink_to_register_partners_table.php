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
        Schema::table('register_partners', function (Blueprint $table) {
            $table->text('part_diet')->nullable()->after('part_height');
            $table->text('part_smoke')->nullable()->after('part_diet');
            $table->text('part_drink')->nullable()->after('part_smoke');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('register_partners', function (Blueprint $table) {
            $table->dropColumn([
                'part_diet',
                'part_smoke',
                'part_drink',
            ]);
        });
    }
};
