<?php

namespace App\Livewire;

use App\Models\DailyFocus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WeeklySummary extends Component
{
    public string $weekStart; // YYYY-MM-DD (lunes)
    public string $weekEnd;   // YYYY-MM-DD (domingo)
    public array $emotionCounts = [];
    public int $emotionTotal = 0;
    public array $dayEndCounts = [];
    public int $dayEndTotal = 0;

    public array $ruminationCounts = [];
    public int $ruminationTotal = 0;

    public array $closuresByDay = []; // para listado por día (opcional)

    public function mount(): void
    {
        // Semana actual (lunes-domingo) según Europe/Madrid
        $now = now();
        $monday = $now->copy()->startOfWeek(1); // 1 = Monday
        $sunday = $monday->copy()->addDays(6);

        $this->weekStart = $monday->toDateString();
        $this->weekEnd = $sunday->toDateString();
        $this->loadEmotionStats();
        $this->loadClosureStats();
    }

    public function prevWeek(): void
    {
        $monday = now()->createFromFormat('Y-m-d', $this->weekStart)->subWeek();
        $this->weekStart = $monday->toDateString();
        $this->weekEnd = $monday->copy()->addDays(6)->toDateString();
        $this->loadEmotionStats();
        $this->loadClosureStats();
    }

    public function nextWeek(): void
    {
        $monday = now()->createFromFormat('Y-m-d', $this->weekStart)->addWeek();
        $this->weekStart = $monday->toDateString();
        $this->weekEnd = $monday->copy()->addDays(6)->toDateString();
        $this->loadEmotionStats();
        $this->loadClosureStats();
    }

    public function render()
    {
        $userId = Auth::id();

        $dailyIds = DailyFocus::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$this->weekStart, $this->weekEnd])
            ->pluck('id');

        $totals = DB::table('focus_blocks')
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed')
            ->whereIn('daily_focus_id', $dailyIds)
            ->first();

        $byType = DB::table('focus_blocks')
            ->join('block_types', 'block_types.id', '=', 'focus_blocks.block_type_id')
            ->selectRaw('block_types.name as type_name, block_types.color as color,
                         COUNT(*) as total,
                         SUM(CASE WHEN focus_blocks.completed_at IS NOT NULL THEN 1 ELSE 0 END) as completed,
                         SUM(COALESCE(focus_blocks.estimated_minutes, 0)) as est_minutes_total,
                         SUM(CASE WHEN focus_blocks.completed_at IS NOT NULL THEN COALESCE(focus_blocks.estimated_minutes, 0) ELSE 0 END) as est_minutes_completed')
            ->whereIn('focus_blocks.daily_focus_id', $dailyIds)
            ->where('block_types.user_id', $userId)
            ->groupBy('block_types.name', 'block_types.color')
            ->orderBy('type_name')
            ->get();

        $total = (int) ($totals->total ?? 0);
        $completed = (int) ($totals->completed ?? 0);
        $pct = $total > 0 ? round(($completed / $total) * 100) : 0;

        return view('livewire.weekly-summary', [
            'total' => $total,
            'completed' => $completed,
            'pct' => $pct,
            'byType' => $byType,
        ])->layout('layouts.app');
    }

    private function loadEmotionStats(): void
    {
        $this->emotionCounts = [];
        $this->emotionTotal = 0;

        $userId = Auth::id();

        $rows = \App\Models\FocusBlock::query()
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

    private function loadClosureStats(): void
    {
        $userId = Auth::id();

        // Reset para evitar “pegado” al cambiar de semana
        $this->dayEndCounts = [];
        $this->dayEndTotal = 0;
        $this->ruminationCounts = [];
        $this->ruminationTotal = 0;
        $this->closuresByDay = [];

        // 1) Conteo de estado final del día
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

        // 2) Conteo de rumiación
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

        // 3) (Opcional) listado por día
        $this->closuresByDay = DailyFocus::query()
            ->where('user_id', $userId)
            ->whereBetween('date', [$this->weekStart, $this->weekEnd])
            ->orderBy('date')
            ->get(['date','day_end_state','rumination_level','takeaway'])
            ->map(fn($d) => [
                'date' => $d->date,
                'day_end_state' => $d->day_end_state,
                'rumination_level' => $d->rumination_level,
                'takeaway' => $d->takeaway,
            ])
            ->toArray();
    }
}
