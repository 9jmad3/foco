<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_block_weekday', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('templates')
                ->cascadeOnDelete();

            $table->foreignId('template_block_id')
                ->constrained('template_blocks')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('weekday'); // 1..7
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            $table->unique(
                ['template_id', 'template_block_id', 'weekday'],
                'uniq_tpl_block_day'
            );

            $table->index(['template_id', 'weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foco_template_block_weekday');
    }
};
