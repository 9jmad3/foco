<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * DailyFocus
 *
 * Representa el “día” (por usuario y fecha).
 * - Guarda el cierre del día (estado, rumiación, takeaway…)
 * - Relaciona los bloques reales del día (focus_blocks)
 *
 * Nota importante sobre el orden:
 * - NO ponemos orderBy por defecto en blocks() para no “pisar” ordenaciones
 *   en componentes (por ejemplo, ordenar por start_time).
 * - Si quieres el orden clásico por sort_order, usa blocksBySortOrder().
 */
class DailyFocus extends Model
{
    /** Tabla explícita (porque no sigue la convención plural) */
    protected $table = 'daily_focus';

    /** Campos permitidos en mass assignment */
    protected $fillable = [
        'user_id',
        'date',

        // historial: qué plantilla/plan se usó para generar ese día (si aplica)
        'template_id_used',

        // cierre del día
        'day_end_state',
        'rumination_level',
        'takeaway',
        'day_closed_at',
    ];

    /** Casts */
    protected $casts = [
        'date' => 'date',
        'template_id_used' => 'integer',
        'day_closed_at' => 'datetime',
    ];

    /**
     * Dueño del día
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bloques del día (RELACIÓN NEUTRA, SIN ORDEN POR DEFECTO).
     *
     * Motivo:
     * Si aquí metemos `orderBy('sort_order')` de forma fija, luego en cualquier
     * query que intente ordenar por start_time (o cualquier otro criterio),
     * Postgres terminará ordenando primero por sort_order.
     *
     * En resumen: esta relación NO impone un ORDER BY.
     */
    public function blocks(): HasMany
    {
        return $this->hasMany(FocusBlock::class);
    }

    /**
     * Bloques del día con el orden “clásico” por sort_order.
     *
     * Úsala en pantallas donde quieras el orden manual/posición.
     * Ejemplo:
     *   $daily->blocksBySortOrder()->get();
     */
    public function blocksBySortOrder(): HasMany
    {
        return $this->hasMany(FocusBlock::class)->orderBy('sort_order');
    }

    /**
     * (Opcional) Bloques del día ordenados por hora de inicio (start_time) y,
     * si no hay hora, al final. sort_order actúa como fallback.
     *
     * Útil si quieres reutilizar este orden en varias vistas.
     * Ejemplo:
     *   $daily->blocksByStartTime()->get();
     */
    public function blocksByStartTime(): HasMany
    {
        return $this->hasMany(FocusBlock::class)
            ->orderByRaw('start_time ASC NULLS LAST')
            ->orderBy('sort_order', 'asc');
    }

    /**
     * Plantilla/plan que se usó para rellenar ese día (si procede).
     */
    public function templateUsed(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id_used');
    }
}
