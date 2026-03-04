<?php

namespace App\Livewire;

use App\Models\DailyFocus;
use App\Models\FocusBlock;
use App\Models\WeekdayBlock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TodayFocus extends Component
{
    /** Registro del día actual del usuario */
    public DailyFocus $daily;

    /**
     * Bloques renderizables en la vista.
     * Cada item: id, title, type_name, color, start_time, estimated_minutes, priority, completed, emotion
     */
    public array $blocks = [];

    /** Overlays UI */
    public bool $showCompletedOverlay = false;
    public bool $showClosingSurvey = false;

    /** Cierre del día */
    public ?string $dayEndState = null;
    public ?string $ruminationLevel = null;
    public string $takeaway = '';

    /**
     * Inicializa:
     * - DailyFocus de hoy
     * - sincroniza HOY con la Semana tipo (weekday_blocks)
     * - carga bloques para pintar
     */
    public function mount(): void
    {
        $userId = Auth::id();
        $today = now()->toDateString();

        $this->daily = DailyFocus::firstOrCreate(
            ['user_id' => $userId, 'date' => $today],
            ['template_id_used' => null]
        );

        // Precarga cierre del día
        $this->dayEndState = $this->daily->day_end_state;
        $this->ruminationLevel = $this->daily->rumination_level;
        $this->takeaway = $this->daily->takeaway ?? '';

        // Sincroniza HOY con semana tipo (hora + prioridad + orden)
        $this->syncTodayWithWeekPlan();

        // Carga para pintar
        $this->refreshBlocks();
    }

    /**
     * Sincroniza los focus_blocks de HOY para que coincidan con el plan semanal del weekday actual.
     *
     * Qué hace:
     * 1) Borra bloques del día que ya no existan en el plan (solo los que vienen de library_block_id)
     * 2) Crea los que falten
     * 3) Actualiza campos (título/tipo/minutos/hora/prioridad) si cambiaron
     * 4) Guarda un sort_order fallback basado en el orden del plan (position)
     *
     * Nota:
     * - La ordenación final en la vista se hace por start_time (NULLS LAST) y luego sort_order.
     */
    private function syncTodayWithWeekPlan(): void
    {
        $userId = Auth::id();

        // Seguridad: solo sincronizamos el día actual
        $dailyDate = $this->daily->date instanceof \Carbon\CarbonInterface
            ? $this->daily->date->toDateString()
            : (string) $this->daily->date;

        if ($dailyDate !== now()->toDateString()) {
            return;
        }

        // Weekday ISO: 1=Lun ... 7=Dom
        $weekday = $this->daily->date instanceof \Carbon\CarbonInterface
            ? (int) $this->daily->date->dayOfWeekIso
            : (int) now()->isoWeekday();

        // Plan del día (incluye: start_time + priority en WeekdayBlock, y datos base en LibraryBlock)
        $plan = WeekdayBlock::query()
            ->where('user_id', $userId)
            ->where('weekday', $weekday)
            ->with('libraryBlock') // title, estimated_minutes, block_type_id
            ->orderBy('position')
            ->get();

        $planIds = $plan->pluck('library_block_id')
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();

        DB::transaction(function () use ($plan, $planIds) {

            // 1) Borrar del día lo que ya no está en el plan (solo los ligados a library_block_id)
            FocusBlock::query()
                ->where('daily_focus_id', $this->daily->id)
                ->whereNotNull('library_block_id')
                ->when(!empty($planIds), fn ($q) => $q->whereNotIn('library_block_id', $planIds))
                ->when(empty($planIds), fn ($q) => $q->whereRaw('1=1')) // plan vacío => eliminar todos los ligados
                ->delete();

            // 2) IDs ya existentes en el día
            $existingIds = FocusBlock::query()
                ->where('daily_focus_id', $this->daily->id)
                ->whereNotNull('library_block_id')
                ->pluck('library_block_id')
                ->map(fn ($v) => (int) $v)
                ->toArray();

            $existingSet = array_flip($existingIds);

            // 3) Crear/actualizar según plan
            $fallbackSort = 10;

            foreach ($plan as $item) {
                $lb = $item->libraryBlock;
                if (!$lb) {
                    $fallbackSort += 10;
                    continue;
                }

                // Normaliza HH:MM a HH:MM:SS si viniera así
                $time = $item->start_time ? (string) $item->start_time : null;
                if ($time && preg_match('/^\d{2}:\d{2}$/', $time)) {
                    $time .= ':00';
                }

                // Prioridad del bloque PARA ESE DÍA (no de biblioteca)
                $priority = $item->priority ?: 'no_importante';
                $allowedPriority = ['urgente', 'importante', 'no_importante'];
                if (!in_array($priority, $allowedPriority, true)) {
                    $priority = 'no_importante';
                }

                $payload = [
                    'block_type_id' => $lb->block_type_id,
                    'title' => $lb->title,
                    'estimated_minutes' => $lb->estimated_minutes,
                    'start_time' => $time,
                    'priority' => $priority,
                    'sort_order' => $fallbackSort, // fallback si no hay hora
                ];

                if (!isset($existingSet[(int) $lb->id])) {
                    FocusBlock::create(array_merge($payload, [
                        'daily_focus_id' => $this->daily->id,
                        'library_block_id' => (int) $lb->id,
                    ]));
                } else {
                    FocusBlock::query()
                        ->where('daily_focus_id', $this->daily->id)
                        ->where('library_block_id', (int) $lb->id)
                        ->update($payload);
                }

                $fallbackSort += 10;
            }
        });

        $this->daily->refresh();
    }

    /**
     * Poll para que, si cambias la Semana tipo en otra pestaña,
     * "Hoy" se actualice solo.
     */
    public function pollRefresh(): void
    {
        $this->syncTodayWithWeekPlan();
        $this->refreshBlocks();
    }

    /**
     * Marca/desmarca un bloque como completado.
     * Si se completa el último, muestra overlay.
     */
    public function toggleCompleted(int $blockId): void
    {
        $block = FocusBlock::query()
            ->where('id', $blockId)
            ->whereHas('dailyFocus', fn ($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        $block->completed_at = $block->completed_at ? null : now();
        $block->save();

        $this->refreshBlocks();

        if ($this->completedCount === count($this->blocks) && count($this->blocks) > 0) {
            $this->showCompletedOverlay = true;
        }
    }

    /** Abre el modal de cierre del día */
    public function openClosingSurvey(): void
    {
        $this->showCompletedOverlay = false;
        $this->showClosingSurvey = true;
    }

    /** Cierra overlays */
    public function closeOverlays(): void
    {
        $this->showCompletedOverlay = false;
        $this->showClosingSurvey = false;
    }

    /**
     * Guarda el cierre del día (solo editable el mismo día).
     */
    public function saveDayClosure(): void
    {
        $dailyDate = $this->daily->date instanceof \Carbon\CarbonInterface
            ? $this->daily->date->toDateString()
            : (string) $this->daily->date;

        if ($dailyDate !== now()->toDateString()) {
            $this->dispatch('toast', message: 'El cierre solo se puede editar el mismo día.');
            $this->showClosingSurvey = false;
            return;
        }

        $allowedState = ['calm','satisfied','tired','tense'];
        $allowedRum   = ['none','controlled','worried'];

        $this->validate([
            'dayEndState' => 'nullable|string',
            'ruminationLevel' => 'nullable|string',
            'takeaway' => 'nullable|string|max:180',
        ]);

        if ($this->dayEndState && !in_array($this->dayEndState, $allowedState, true)) {
            $this->dayEndState = null;
        }
        if ($this->ruminationLevel && !in_array($this->ruminationLevel, $allowedRum, true)) {
            $this->ruminationLevel = null;
        }

        $this->daily->update([
            'day_end_state' => $this->dayEndState,
            'rumination_level' => $this->ruminationLevel,
            'takeaway' => trim($this->takeaway) ?: null,
            'day_closed_at' => now(),
        ]);

        $this->daily->refresh();

        $this->dayEndState = $this->daily->day_end_state;
        $this->ruminationLevel = $this->daily->rumination_level;
        $this->takeaway = $this->daily->takeaway ?? '';

        $this->showClosingSurvey = false;
        $this->showCompletedOverlay = false;

        $this->dispatch('toast', message: 'Cierre guardado');
    }

    /**
     * Asigna emoción a un bloque COMPLETADO.
     */
    public function setEmotion(int $blockId, string $emotion): void
    {
        $allowed = ['calm','good','neutral','tired','frustrated'];
        if (!in_array($emotion, $allowed, true)) return;

        $block = FocusBlock::query()
            ->where('id', $blockId)
            ->whereHas('dailyFocus', fn ($q) => $q->where('user_id', Auth::id()))
            ->firstOrFail();

        if (!$block->completed_at) return;

        $block->emotion = $emotion;
        $block->save();

        $this->refreshBlocks();
    }

    /**
     * Borra bloques del día (y el cierre) y vuelve a reconstruir HOY desde la Semana tipo.
     */
    public function reapplyTodayTemplate(): void
    {
        DB::transaction(function () {
            $this->daily->blocks()->delete();

            $this->daily->update([
                'template_id_used' => null,
                'day_end_state' => null,
                'rumination_level' => null,
                'takeaway' => null,
                'day_closed_at' => null,
            ]);
        });

        $this->daily->refresh();

        $this->syncTodayWithWeekPlan();
        $this->refreshBlocks();

        $this->dispatch('toast', message: 'Semana tipo reaplicada');
    }

    /**
     * Carga bloques para pintar y los ordena por:
     * 1) start_time (NULLS LAST)
     * 2) sort_order como fallback
     */
    private function refreshBlocks(): void
    {
        $this->blocks = $this->daily->blocks()
            ->with(['blockType', 'libraryBlock'])
            ->orderByRaw('start_time ASC NULLS LAST') // Postgres
            ->orderBy('sort_order', 'asc')            // fallback
            ->get()
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'title' => $b->title,
                    'start_time' => $b->start_time ? substr((string) $b->start_time, 0, 5) : null,
                    'estimated_minutes' => $b->estimated_minutes,
                    'priority' => $b->priority ?: 'no_importante',
                    'completed' => (bool) $b->completed_at,
                    'emotion' => $b->emotion,
                    'color' => $b->blockType?->color ?? '#234F3F',
                    'type_name' => $b->blockType?->name ?? '',
                    'notes' => $b->libraryBlock?->notes,
                ];
            })
            ->toArray();
    }

    /** Nº de bloques completados (computed) */
    public function getCompletedCountProperty(): int
    {
        return collect($this->blocks)->where('completed', true)->count();
    }

    /** Día completo si todos los bloques están completados */
    public function getIsDayCompleteProperty(): bool
    {
        $total = count($this->blocks);
        return $total > 0 && $this->completedCount === $total;
    }

    /** El cierre solo se edita el mismo día */
    public function getCanEditClosureProperty(): bool
    {
        $dailyDate = $this->daily->date instanceof \Carbon\CarbonInterface
            ? $this->daily->date->toDateString()
            : (string) $this->daily->date;

        return $dailyDate === now()->toDateString();
    }

    public function render()
    {
        return view('livewire.today-focus', [
            'total' => count($this->blocks),
            'completed' => $this->completedCount,
        ])->layout('layouts.app');
    }
}
