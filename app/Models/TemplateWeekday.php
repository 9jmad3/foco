<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateWeekday extends Model
{
    protected $fillable = ['template_id', 'weekday'];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
