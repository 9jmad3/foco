<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('focus_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_focus_id')->constrained('daily_focus')->cascadeOnDelete();
            $table->foreignId('block_type_id')->constrained('block_types')->cascadeOnDelete();

            $table->string('title', 180);
            $table->unsignedSmallInteger('estimated_minutes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['daily_focus_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('focus_blocks');
    }
};
