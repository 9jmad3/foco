<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'max_daily_blocks',
        'strict_mode',
        'default_template_id',
    ];

    protected $casts = [
        'strict_mode' => 'boolean',
        'max_daily_blocks' => 'integer',
        'default_template_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function defaultTemplate(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'default_template_id');
    }
}
