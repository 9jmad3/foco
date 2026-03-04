<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('library_blocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('block_type_id')
                ->constrained('block_types')
                ->cascadeOnDelete();

            $table->string('title', 180);
            $table->unsignedSmallInteger('estimated_minutes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'block_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_blocks');
    }
};
