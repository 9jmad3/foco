<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FocusBlock extends Model
{
    protected $fillable = [
        'daily_focus_id',
        'block_type_id',
        'title',
        'estimated_minutes',
        'completed_at',
        'sort_order',
        'emotion',
        'library_block_id',
        'start_time',
    ];

    protected $casts = [
        'estimated_minutes' => 'integer',
        'completed_at' => 'datetime',
        'sort_order' => 'integer',
        'start_time' => 'string',
    ];

    public function dailyFocus(): BelongsTo
    {
        return $this->belongsTo(DailyFocus::class);
    }

    public function blockType(): BelongsTo
    {
        return $this->belongsTo(BlockType::class);
    }

    public function libraryBlock(): BelongsTo
    {
        return $this->belongsTo(\App\Models\LibraryBlock::class);
    }
}
