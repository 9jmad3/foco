<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('weekday_blocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('weekday'); // 1..7 (Lun..Dom)

            $table->foreignId('library_block_id')
                ->constrained('library_blocks')
                ->cascadeOnDelete();

            $table->integer('position')->default(0);

            $table->timestamps();

            $table->index(['user_id', 'weekday', 'position']);

            // Evita duplicar el mismo bloque en el mismo día
            $table->unique(['user_id', 'weekday', 'library_block_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekday_blocks');
    }
};
