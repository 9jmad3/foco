<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo que representa una reflexión semanal generada
 * a partir de los datos de bloques y cierres del usuario.
 */
class WeeklyReflection extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'week_end',
        'summary_text',
        'data_snapshot'
    ];

    protected $casts = [
        'data_snapshot' => 'array'
    ];
}
