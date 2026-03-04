<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeekdayBlock extends Model
{
    protected $fillable = [
        'user_id',
        'weekday',
        'library_block_id',
        'position',
        'start_time',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'position' => 'integer',
        'start_time' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function libraryBlock(): BelongsTo
    {
        return $this->belongsTo(LibraryBlock::class);
    }
}
