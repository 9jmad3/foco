<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryBlock extends Model
{
    protected $fillable = [
        'user_id',
        'block_type_id',
        'title',
        'estimated_minutes',
        'notes',
    ];

    protected $casts = [
        'estimated_minutes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function blockType(): BelongsTo
    {
        return $this->belongsTo(BlockType::class);
    }
}
