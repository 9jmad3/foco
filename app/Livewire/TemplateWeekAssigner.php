<?php

namespace App\Livewire;

use App\Models\Template;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TemplateWeekAssigner extends Component
{
    public ?int $selectedTemplateId = null;

    public int $activeWeekday = 1; // 1..7
    public array $weekBlocks = []; // [weekday => []]

    public function mount(): void
    {
        foreach (range(1, 7) as $d) $this->weekBlocks[$d] = [];

        $this->selectDefaultOrFirst();
        if ($this->selectedTemplateId) {
            $this->loadWeekBlocks();
        }
    }

    private function selectDefaultOrFirst(): void
    {
        $userId = Auth::id();

        $default = Template::query()
            ->where('user_id', $userId)
            ->where('is_default', true)
            ->orderBy('id')
            ->first();

        $first = $default ?: Template::query()->where('user_id', $userId)->orderBy('id')->first();
        $this->selectedTemplateId = $first?->id;
    }

    public function selectTemplate(int $id): void
    {
        $tpl = Template::query()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $this->selectedTemplateId = $tpl->id;
        $this->loadWeekBlocks();
    }

    protected function loadWeekBlocks(): void
    {
        foreach (range(1, 7) as $d) $this->weekBlocks[$d] = [];
        if (!$this->selectedTemplateId) return;

        $rows = DB::table('template_block_weekday as tbw')
            ->join('template_blocks as b', 'b.id', '=', 'tbw.template_block_id')
            ->leftJoin('block_types as t', 't.id', '=', 'b.block_type_id')
            ->where('tbw.template_id', $this->selectedTemplateId)
            ->orderBy('tbw.weekday')
            ->orderBy('tbw.position')
            ->select([
                'tbw.id as pivot_id',
                'tbw.weekday',
                'tbw.position',
                'b.id as block_id',
                'b.title',
                'b.estimated_minutes',
                't.name as type_name',
                't.color as type_color',
            ])
            ->get();

        foreach ($rows as $r) {
            $day = (int) $r->weekday;
            $this->weekBlocks[$day][] = [
                'pivot_id' => (int) $r->pivot_id,
                'block_id' => (int) $r->block_id,
                'title' => (string) $r->title,
                'minutes' => (int) ($r->estimated_minutes ?? 0),
                'type' => (string) ($r->type_name ?? ''),
                'color' => (string) ($r->type_color ?? '#10B981'),
                'position' => (int) $r->position,
            ];
        }
    }

    public function assignBlockToDay(int $blockId): void
    {
        if (!$this->selectedTemplateId) return;

        $weekday = max(1, min(7, $this->activeWeekday));

        $exists = DB::table('template_block_weekday')
            ->where('template_id', $this->selectedTemplateId)
            ->where('template_block_id', $blockId)
            ->where('weekday', $weekday)
            ->exists();

        if ($exists) {
            $this->dispatch('toast', message: 'Ya estaba asignado');
            return;
        }

        $maxPos = (int) DB::table('template_block_weekday')
            ->where('template_id', $this->selectedTemplateId)
            ->where('weekday', $weekday)
            ->max('position');

        DB::table('template_block_weekday')->insert([
            'template_id' => $this->selectedTemplateId,
            'template_block_id' => $blockId,
            'weekday' => $weekday,
            'position' => $maxPos + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Asignado');
    }

    public function unassignPivot(int $pivotId): void
    {
        if (!$this->selectedTemplateId) return;

        DB::table('template_block_weekday')
            ->where('id', $pivotId)
            ->where('template_id', $this->selectedTemplateId)
            ->delete();

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Quitado');
    }

    public function render()
    {
        $userId = Auth::id();

        $templates = Template::query()
            ->where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $selected = $this->selectedTemplateId
            ? Template::query()->where('user_id', $userId)->with('blocks.blockType')->find($this->selectedTemplateId)
            : null;

        $blocks = $selected?->blocks?->sortBy('sort_order') ?? collect();

        return view('livewire.template-week-assigner', compact('templates','selected','blocks'))
            ->layout('layouts.app');
    }
}
