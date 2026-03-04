<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->foreign('default_template_id')
                ->references('id')->on('templates')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropForeign(['default_template_id']);
        });
    }
};
