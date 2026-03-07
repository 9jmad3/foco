<?php

namespace App\Livewire;

use App\Models\DailyFocus;
use App\Models\FocusBlock;
use App\Models\WeeklyReflection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WeeklySummary extends Component
{
    /**
     * Fecha de inicio de la semana seleccionada (lunes) en formato Y-m-d.
     */
    public string $weekStart;

    /**
     * Fecha de fin de la semana seleccionada (domingo) en formato Y-m-d.
     */
    public string $weekEnd;

    /**
     * Conteo de emociones de bloques completados.
     * Ejemplo: ['good' => 3, 'calm' => 2]
     */
    public array $emotionCounts = [];

    /**
     * Total de bloques completados que tienen emoción registrada.
     */
    public int $emotionTotal = 0;

    /**
     * Conteo de estados de cierre del día.
     * Ejemplo: ['satisfied' => 2, 'tired' => 1]
     */
    public array $dayEndCounts = [];

    /**
     * Total de cierres con estado final registrado.
     */
    public int $dayEndTotal = 0;

    /**
     * Conteo de niveles de rumiación.
     */
    public array $ruminationCounts = [];

    /**
     * Total de cierres con rumiación registrada.
     */
    public int $ruminationTotal = 0;

    /**
     * Listado de cierres por día para mostrar en la parte inferior.
     */
    public array $closuresByDay = [];

    /**
     * Texto generado y guardado de la lectura/reflexión semanal.
     */
    public ?string $weeklyReflection = null;

    /**
     * Total de bloques de la semana.
     */
    public int $total = 0;

    /**
     * Total de bloques completados de la semana.
     */
    public int $completed = 0;

    /**
     * Porcentaje de cumplimiento semanal.
     */
    public int $pct = 0;

    /**
     * Desglose por tipo de bloque.
     */
    public array $byType = [];

    /**
     * Inicializa la semana actual y carga todos los datos del resumen.
     */
    public function mount(): void
    {
        $now = now();
        $monday = $now->copy()->startOfWeek(Carbon::MONDAY);
        $sunday = $monday->copy()->addDays(6);

        $this->weekStart = $monday->toDateString();
        $this->weekEnd = $sunday->toDateString();

        $this->reloadAllStats();
    }

    /**
     * Cambia a la semana anterior y recarga todas las estadísticas.
     */
    public function prevWeek(): void
    {
        $monday = Carbon::createFromFormat('Y-m-d', $this->weekStart)->subWeek();

        $this->weekStart = $monday->toDateString();
        $this->weekEnd = $monday->copy()->addDays(6)->toDateString();

        $this->reloadAllStats();
    }

    /**
     * Cambia a la semana siguiente y recarga todas las estadísticas.
     */
    public function nextWeek(): void
    {
        $monday = Carbon::createFromFormat('Y-m-d', $this->weekStart)->addWeek();

        $this->weekStart = $monday->toDateString();
        $this->weekEnd = $monday->copy()->addDays(6)->toDateString();

        $this->reloadAllStats();
    }

    /**
     * Recarga todo el resumen de la semana actual.
     */
    private function reloadAllStats(): void
    {
        $this->loadWeeklyStats();
        $this->loadEmotionStats();
        $this->loadClosureStats();
        $this->loadWeeklyReflection();
    }

    /**
     * Devuelve el rango semanal en formato humano.
     * Ejemplo: "del lunes 1 de enero al domingo 7 de enero"
     */
    private function getHumanWeekRange(): string
    {
        $start = Carbon::parse($this->weekStart)->locale('es');
        $end = Carbon::parse($this->weekEnd)->locale('es');

        return 'del '
            . mb_strtolower($start->translatedFormat('l j \d\e F'))
            . ' al '
            . mb_strtolower($end->translatedFormat('l j \d\e F'));
    }

    /**
     * Devuelve una fecha en formato corto d-m-Y.
     * Ejemplo: "01-10-2026"
     */
    public function formatShortDate(string $date): string
    {
        return Carbon::parse($date)->format('d-m-Y');
    }

    /**
     * Carga:
     * - total de bloques de la semana
     * - bloques completados
     * - porcentaje de cumplimiento
     * - desglose por tipo de bloque
     */
    private function loadWeeklyStats(): void
    {
        $userId = Auth::id();

        $dailyIds = DailyFocus::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$this->weekStart, $this->weekEnd])
            ->pluck('id');

        $totals = DB::table('focus_blocks')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed
            ')
            ->whereIn('daily_focus_id', $dailyIds)
            ->first();

        $this->byType = DB::table('focus_blocks')
            ->join('block_types', 'block_types.id', '=', 'focus_blocks.block_type_id')
            ->selectRaw('
                block_types.name as type_name,
                block_types.color as color,
                COUNT(*) as total,
                SUM(CASE WHEN focus_blocks.completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed,
                SUM(COALESCE(focus_blocks.estimated_minutes, 0)) as est_minutes_total,
                SUM(
                    CASE
                        WHEN focus_blocks.completed_at IS NOT NULL
                        THEN COALESCE(focus_blocks.estimated_minutes, 0)
                        ELSE 0
                    END
                ) as est_minutes_completed
            ')
            ->whereIn('focus_blocks.daily_focus_id', $dailyIds)
            ->where('block_types.user_id', $userId)
            ->groupBy('block_types.name', 'block_types.color')
            ->orderBy('type_name')
            ->get()
            ->toArray();

        $this->total = (int) ($totals->total ?? 0);
        $this->completed = (int) ($totals->completed ?? 0);
        $this->pct = $this->total > 0
            ? (int) round(($this->completed / $this->total) * 100)
            : 0;
    }

    /**
     * Carga la reflexión semanal guardada para el usuario y la semana actual.
     * Si no existe, deja la propiedad a null.
     */
    private function loadWeeklyReflection(): void
    {
        $reflection = WeeklyReflection::query()
            ->where('user_id', Auth::id())
            ->where('week_start', $this->weekStart)
            ->first();

        $this->weeklyReflection = $reflection?->summary_text;
    }

    /**
     * Genera una lectura/reflexión semanal basada en reglas
     * y la guarda en la base de datos.
     */
    public function generateWeeklyReflection(): void
    {
        // Recarga todos los datos antes de regenerar,
        // por si el usuario ha cambiado emociones o cierres.
        $this->loadWeeklyStats();
        $this->loadEmotionStats();
        $this->loadClosureStats();

        if ($this->pct >= 80) {
            $completionText = 'ha sido una semana bastante constante en cuanto a cumplimiento de bloques.';
        } elseif ($this->pct >= 50) {
            $completionText = 'ha sido una semana con avance sostenido, aunque con cierta irregularidad.';
        } else {
            $completionText = 'la semana parece haber tenido más dificultad para mantener el ritmo de bloques.';
        }

        $goodCount = (int) ($this->emotionCounts['good'] ?? 0);
        $calmCount = (int) ($this->emotionCounts['calm'] ?? 0);
        $neutralCount = (int) ($this->emotionCounts['neutral'] ?? 0);
        $tiredCount = (int) ($this->emotionCounts['tired'] ?? 0);
        $frustratedCount = (int) ($this->emotionCounts['frustrated'] ?? 0);

        $positiveCount = $goodCount + $calmCount;
        $negativeCount = $tiredCount + $frustratedCount;

        if ($this->emotionTotal === 0) {
            $emotionText = 'no hay suficientes emociones registradas para extraer una lectura clara.';
        } elseif ($negativeCount > $positiveCount) {
            $emotionText = 'en los bloques completados aparece más carga emocional de la deseada, con señales de cansancio o frustración.';
        } elseif ($positiveCount > $negativeCount) {
            $emotionText = 'en general, los bloques completados se asocian a sensaciones más bien positivas, de bienestar o calma.';
        } elseif ($neutralCount > 0) {
            $emotionText = 'emocionalmente la semana ha sido bastante mixta, sin una sensación dominante del todo clara.';
        } else {
            $emotionText = 'las emociones de la semana han sido variadas y cambiantes.';
        }

        $state = collect($this->dayEndCounts)->sortDesc()->keys()->first();

        $stateText = match ($state) {
            'satisfied' => 'en los cierres del día suele aparecer una sensación de satisfacción.',
            'calm' => 'los días tienden a terminar con cierta tranquilidad.',
            'tired' => 'los cierres reflejan sobre todo cansancio acumulado.',
            'tense' => 'en varios días aparece tensión al terminar la jornada.',
            default => '',
        };

        $humanWeekRange = ucfirst($this->getHumanWeekRange());

        $text = trim("{$humanWeekRange}, {$completionText} {$emotionText} {$stateText} En conjunto, la semana transmite implicación y continuidad.");

        WeeklyReflection::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'week_start' => $this->weekStart,
            ],
            [
                'week_end' => $this->weekEnd,
                'summary_text' => $text,
                'data_snapshot' => [
                    'completed' => $this->completed,
                    'total' => $this->total,
                    'pct' => $this->pct,
                    'emotion_counts' => $this->emotionCounts,
                    'day_end_counts' => $this->dayEndCounts,
                    'rumination_counts' => $this->ruminationCounts,
                    'closures_by_day' => $this->closuresByDay,
                ],
            ]
        );

        $this->weeklyReflection = $text;
    }

    /**
     * Carga estadísticas emocionales de los bloques completados:
     * cuántos bloques terminan con cada emoción.
     */
    private function loadEmotionStats(): void
    {
        $this->emotionCounts = [];
        $this->emotionTotal = 0;

        $userId = Auth::id();

        $rows = FocusBlock::query()
            ->whereHas('dailyFocus', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->whereBetween('date', [$this->weekStart, $this->weekEnd]);
            })
            ->whereNotNull('completed_at')
            ->whereNotNull('emotion')
            ->select('emotion', DB::raw('count(*) as c'))
            ->groupBy('emotion')
            ->pluck('c', 'emotion')
            ->toArray();

        $this->emotionCounts = $rows;
        $this->emotionTotal = array_sum($rows);
    }

    /**
     * Carga:
     * - conteo de estados finales del día
     * - conteo de rumiación
     * - listado de cierres por día
     */
    private function loadClosureStats(): void
    {
        $userId = Auth::id();

        $this->dayEndCounts = [];
        $this->dayEndTotal = 0;
        $this->ruminationCounts = [];
        $this->ruminationTotal = 0;
        $this->closuresByDay = [];

        $rows = DailyFocus::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$this->weekStart, $this->weekEnd])
            ->whereNotNull('day_end_state')
            ->select('day_end_state', DB::raw('count(*) as c'))
            ->groupBy('day_end_state')
            ->pluck('c', 'day_end_state')
            ->toArray();

        $this->dayEndCounts = $rows;
        $this->dayEndTotal = array_sum($rows);

        $rows2 = DailyFocus::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$this->weekStart, $this->weekEnd])
            ->whereNotNull('rumination_level')
            ->select('rumination_level', DB::raw('count(*) as c'))
            ->groupBy('rumination_level')
            ->pluck('c', 'rumination_level')
            ->toArray();

        $this->ruminationCounts = $rows2;
        $this->ruminationTotal = array_sum($rows2);

        $this->closuresByDay = DailyFocus::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$this->weekStart, $this->weekEnd])
            ->orderBy('date')
            ->get(['date', 'day_end_state', 'rumination_level', 'takeaway'])
            ->map(fn ($d) => [
                'date' => $d->date,
                'day_end_state' => $d->day_end_state,
                'rumination_level' => $d->rumination_level,
                'takeaway' => $d->takeaway,
            ])
            ->toArray();
    }

    /**
     * Renderiza la vista.
     */
    public function render()
    {
        return view('livewire.weekly-summary', [
            'total' => $this->total,
            'completed' => $this->completed,
            'pct' => $this->pct,
            'byType' => $this->byType,
        ])->layout('layouts.app');
    }
}
