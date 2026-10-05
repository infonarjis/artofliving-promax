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
        Schema::table('custom_chat_conversation_message', function (Blueprint $table) {
            $table->tinyInteger('chat_status')
                ->default(0)
                ->comment('0 = sent, 1 = delivered, 2 = seen')
                ->after('is_read'); // change position if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_chat_conversation_message', function (Blueprint $table) {
            Schema::table('custom_chat_conversation_message', function (Blueprint $table) {
                $table->dropColumn('chat_status');
            });
        });
    }
};
