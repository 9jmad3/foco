<?php
// app/Livewire/WeekAssigner.php

namespace App\Livewire;

use App\Models\LibraryBlock;
use App\Models\WeekdayBlock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WeekAssigner extends Component
{
    public int $activeWeekday = 1; // 1..7 (Lun..Dom)

    /** cache simple para sidebar */
    public array $weekBlocks = []; // [weekday => array(items)]

    public function mount(): void
    {
        foreach (range(1, 7) as $d) $this->weekBlocks[$d] = [];
        $this->loadWeekBlocks();
    }

    private function loadWeekBlocks(): void
    {
        $userId = Auth::id();

        foreach (range(1, 7) as $d) $this->weekBlocks[$d] = [];

        $rows = WeekdayBlock::query()
            ->where('user_id', $userId)
            ->with('libraryBlock.blockType')
            ->orderBy('weekday')
            ->orderBy('position')
            ->get();

        foreach ($rows as $r) {
            $lb = $r->libraryBlock;

            $this->weekBlocks[(int) $r->weekday][] = [
                'id' => (int) $r->id,
                'library_block_id' => (int) $r->library_block_id,
                'title' => (string) ($lb?->title ?? ''),
                'minutes' => (int) ($lb?->estimated_minutes ?? 0),
                'type' => (string) ($lb?->blockType?->name ?? ''),
                'color' => (string) ($lb?->blockType?->color ?? '#10B981'),
                'position' => (int) $r->position,
                'start_time' => $r->start_time ? (string) $r->start_time : null,
                'priority' => $r->priority ?: 'no_importante',
                'notes' => (string) ($lb?->notes ?? ''),
            ];
        }
    }

    public function assign(int $libraryBlockId): void
    {
        $userId = Auth::id();
        $weekday = max(1, min(7, $this->activeWeekday));

        $exists = WeekdayBlock::query()
            ->where('user_id', $userId)
            ->where('weekday', $weekday)
            ->where('library_block_id', $libraryBlockId)
            ->exists();

        if ($exists) {
            $this->dispatch('toast', message: 'Ya estaba asignado');
            return;
        }

        $maxPos = (int) WeekdayBlock::query()
            ->where('user_id', $userId)
            ->where('weekday', $weekday)
            ->max('position');

        WeekdayBlock::create([
            'user_id' => $userId,
            'weekday' => $weekday,
            'library_block_id' => $libraryBlockId,
            'position' => $maxPos + 10,
            'start_time' => null,
            'priority' => 'no_importante'
        ]);

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Asignado');
    }

    public function updatePriority(int $weekdayBlockId, ?string $priority): void
    {
        $userId = Auth::id();

        $priority = trim((string) $priority);

        $allowed = ['urgente', 'importante', 'no_importante'];
        if (!in_array($priority, $allowed, true)) {
            $priority = 'no_importante';
        }

        WeekdayBlock::query()
            ->where('id', $weekdayBlockId)
            ->where('user_id', $userId)
            ->update(['priority' => $priority]);

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Prioridad guardada');
    }

    public function unassign(int $weekdayBlockId): void
    {
        $userId = Auth::id();

        WeekdayBlock::query()
            ->where('user_id', $userId)
            ->where('id', $weekdayBlockId)
            ->delete();

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Quitado');
    }

    public function move(int $weekdayBlockId, string $direction): void
    {
        $userId = Auth::id();
        $weekday = max(1, min(7, $this->activeWeekday));

        $items = WeekdayBlock::query()
            ->where('user_id', $userId)
            ->where('weekday', $weekday)
            ->orderBy('position')
            ->get();

        $idx = $items->search(fn($x) => (int) $x->id === (int) $weekdayBlockId);
        if ($idx === false) return;

        $swapWith = $direction === 'up' ? $idx - 1 : $idx + 1;
        if ($swapWith < 0 || $swapWith >= $items->count()) return;

        $a = $items[$idx];
        $b = $items[$swapWith];

        DB::transaction(function () use ($a, $b) {
            $tmp = $a->position;
            $a->position = $b->position;
            $b->position = $tmp;
            $a->save();
            $b->save();
        });

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Orden actualizado');
    }

    public function render()
    {
        $userId = Auth::id();

        $library = LibraryBlock::query()
            ->where('user_id', $userId)
            ->with('blockType')
            ->orderBy('title')
            ->get();

        // sidebar ya está en $weekBlocks, se refresca en mount/acciones

        return view('livewire.week-assigner', [
            'library' => $library,
        ])->layout('layouts.app');
    }

    public function updateStartTime(int $weekdayBlockId, ?string $time): void
    {
        $userId = Auth::id();

        $time = trim((string) $time);

        if ($time === '') {
            $time = null;
        } elseif (preg_match('/^\d{1,2}:\d{1,2}$/', $time)) {
            [$h, $m] = explode(':', $time);
            $time = sprintf('%02d:%02d:00', $h, $m);
        }

        WeekdayBlock::query()
            ->where('id', $weekdayBlockId)
            ->where('user_id', $userId)
            ->update(['start_time' => $time]);

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Hora guardada');
    }
}
