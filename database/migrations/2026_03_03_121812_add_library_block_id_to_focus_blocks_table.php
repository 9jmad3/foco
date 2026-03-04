<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('focus_blocks', function (Blueprint $table) {
            $table->foreignId('library_block_id')
                ->nullable()
                ->after('daily_focus_id')
                ->constrained('library_blocks')
                ->nullOnDelete();

            $table->index(['library_block_id']);
        });
    }

    public function down(): void
    {
        Schema::table('focus_blocks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('library_block_id');
        });
    }
};
