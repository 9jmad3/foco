<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('template_weekdays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('templates')->cascadeOnDelete();
            // ISO-8601: 1=Lunes ... 7=Domingo
            $table->unsignedTinyInteger('weekday');
            $table->timestamps();

            $table->unique(['template_id', 'weekday']);
            $table->index(['weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_weekdays');
    }
};
