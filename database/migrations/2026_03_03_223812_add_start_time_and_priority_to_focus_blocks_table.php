<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('focus_blocks', function (Blueprint $table) {
            // prioridad del bloque de ese día
            $table->string('priority', 20)->default('no_importante')->after('start_time');
        });
    }

    public function down(): void
    {
        Schema::table('focus_blocks', function (Blueprint $table) {
            $table->dropColumn(['priority']);
        });
    }
};
