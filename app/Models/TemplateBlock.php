<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateBlock extends Model
{
    protected $fillable = [
        'template_id',
        'block_type_id',
        'title',
        'estimated_minutes',
        'sort_order',
    ];

    protected $casts = [
        'estimated_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function blockType(): BelongsTo
    {
        return $this->belongsTo(BlockType::class);
    }
}
