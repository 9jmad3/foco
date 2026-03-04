<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('daily_focus', function (Blueprint $table) {
            $table->string('day_end_state', 30)->nullable();      // calm, satisfied, tired, tense...
            $table->string('rumination_level', 30)->nullable();   // none, controlled, worried
            $table->string('takeaway', 180)->nullable();          // "Hoy me llevo..."
            $table->timestamp('day_closed_at')->nullable();       // cuando guardó cierre
        });
    }

    public function down(): void
    {
        Schema::table('daily_focus', function (Blueprint $table) {
            $table->dropColumn(['day_end_state','rumination_level','takeaway','day_closed_at']);
        });
    }
};
