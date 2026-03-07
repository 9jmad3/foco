<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla donde se guardarán las reflexiones semanales
     * generadas por el usuario a partir de sus datos de la semana.
     */
    public function up(): void
    {
        Schema::create('weekly_reflections', function (Blueprint $table) {
            $table->id();

            // Usuario al que pertenece la reflexión
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Semana a la que corresponde
            $table->date('week_start'); // lunes
            $table->date('week_end');   // domingo

            // Texto generado
            $table->text('summary_text');

            // Snapshot opcional de los datos usados para generar el texto
            $table->json('data_snapshot')->nullable();

            $table->timestamps();

            // Solo una reflexión por usuario y semana
            $table->unique(['user_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_reflections');
    }
};
