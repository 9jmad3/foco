<?php

namespace App\Livewire;

use App\Models\BlockType;
use App\Models\Template;
use App\Models\TemplateBlock;
use App\Models\UserSetting;
use App\Models\TemplateWeekday;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TemplatesManager extends Component
{
    /** UI state */
    public ?int $selectedTemplateId = null;

    /** Template form */
    public string $templateName = '';

    /** Block form */
    public string $blockTitle = '';
    public ?int $blockTypeId = null;
    public ?int $blockEstimated = null;

    /** Editing blocks */
    public array $editingMinutes = []; // [blockId => minutes]
    public array $editingTitle = [];   // [blockId => title]

    /** (Legacy) Días "de activación" de plantilla */
    public array $selectedWeekdays = []; // [1..7]

    /** NUEVO: editor semanal */
    public int $activeWeekday = 1;      // 1..7
    public array $weekBlocks = [];      // [weekday => [items...]]

    public function mount(): void
    {
        // Inicializa estructura para evitar undefined indexes en blade
        $this->weekBlocks = [];
        foreach (range(1, 7) as $d) {
            $this->weekBlocks[$d] = [];
        }

        $this->selectDefaultOrFirst();
        if ($this->selectedTemplateId) {
            $this->selectTemplate($this->selectedTemplateId);
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
        $this->templateName = $first?->name ?? '';
    }

    public function selectTemplate(int $id): void
    {
        $tpl = Template::query()->where('user_id', Auth::id())->findOrFail($id);

        $this->selectedTemplateId = $tpl->id;
        $this->templateName = $tpl->name;

        $this->editingMinutes = [];
        $this->editingTitle = [];

        // (Legacy) si sigues usando template_weekdays
        $this->selectedWeekdays = TemplateWeekday::query()
            ->where('template_id', $tpl->id)
            ->pluck('weekday')
            ->map(fn($v) => (int) $v)
            ->toArray();

        // NUEVO: carga los bloques asignados por día
        $this->loadWeekBlocks();
    }

    public function createTemplate(): void
    {
        $this->validate([
            'templateName' => 'required|string|max:120',
        ]);

        $tpl = Template::create([
            'user_id' => Auth::id(),
            'name' => trim($this->templateName),
            'is_default' => false,
        ]);

        $this->selectedTemplateId = $tpl->id;

        // reset editor semanal
        $this->activeWeekday = 1;
        $this->loadWeekBlocks();

        $this->dispatch('toast', message: 'Plantilla creada');
    }

    public function saveTemplateName(): void
    {
        $this->validate([
            'templateName' => 'required|string|max:120',
        ]);

        $tpl = $this->selectedTemplate();
        $tpl->update(['name' => trim($this->templateName)]);
        $this->dispatch('toast', message: 'Nombre guardado');
    }

    public function deleteSelectedTemplate(): void
    {
        $tpl = $this->selectedTemplate();

        DB::transaction(function () use ($tpl) {
            if ($tpl->is_default) {
                UserSetting::where('user_id', Auth::id())->update(['default_template_id' => null]);
            }

            // OJO: si por lo que sea no tienes ON DELETE CASCADE en pivote, lo borramos a mano
            DB::table('template_block_weekday')->where('template_id', $tpl->id)->delete();

            $tpl->delete();
        });

        $this->templateName = '';
        $this->selectedTemplateId = null;

        // limpia panel semanal
        foreach (range(1, 7) as $d) {
            $this->weekBlocks[$d] = [];
        }

        $this->selectDefaultOrFirst();
        if ($this->selectedTemplateId) {
            $this->selectTemplate($this->selectedTemplateId);
        }

        $this->dispatch('toast', message: 'Plantilla eliminada');
    }

    public function setAsDefault(): void
    {
        $tpl = $this->selectedTemplate();
        $userId = Auth::id();

        DB::transaction(function () use ($tpl, $userId) {
            Template::where('user_id', $userId)->update(['is_default' => false]);
            $tpl->update(['is_default' => true]);

            $settings = UserSetting::firstOrCreate(
                ['user_id' => $userId],
                ['max_daily_blocks' => 3, 'strict_mode' => true]
            );
            $settings->update(['default_template_id' => $tpl->id]);
        });

        $this->dispatch('toast', message: 'Plantilla predeterminada actualizada');
    }

    public function addBlock(): void
    {
        $this->validate([
            'blockTitle' => 'required|string|max:180',
            'blockTypeId' => 'required|integer',
            'blockEstimated' => 'nullable|integer|min:1|max:999',
        ]);

        $tpl = $this->selectedTemplate();

        $maxSort = (int) $tpl->blocks()->max('sort_order');

        TemplateBlock::create([
            'template_id' => $tpl->id,
            'block_type_id' => $this->blockTypeId,
            'title' => trim($this->blockTitle),
            'estimated_minutes' => $this->blockEstimated,
            'sort_order' => $maxSort + 10,
        ]);

        $this->blockTitle = '';
        $this->blockEstimated = null;

        // refresca panel semanal (por si quieres asignarlo al momento)
        $this->loadWeekBlocks();

        $this->dispatch('toast', message: 'Bloque añadido');
    }

    public function deleteBlock(int $blockId): void
    {
        $tpl = $this->selectedTemplate();

        DB::transaction(function () use ($tpl, $blockId) {
            // borra también asignaciones del pivote
            DB::table('template_block_weekday')
                ->where('template_id', $tpl->id)
                ->where('template_block_id', $blockId)
                ->delete();

            TemplateBlock::query()
                ->where('id', $blockId)
                ->where('template_id', $tpl->id)
                ->delete();
        });

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Bloque eliminado');
    }

    public function saveBlockEdits(int $blockId): void
    {
        $tpl = $this->selectedTemplate();

        $title = $this->editingTitle[$blockId] ?? null;
        $mins = $this->editingMinutes[$blockId] ?? null;

        $data = [];
        if (!is_null($title)) {
            $data['title'] = trim((string) $title);
        }
        if ($mins === '' || is_null($mins)) {
            $data['estimated_minutes'] = null;
        } else {
            $data['estimated_minutes'] = (int) $mins;
        }

        if (!$data) return;

        TemplateBlock::query()
            ->where('id', $blockId)
            ->where('template_id', $tpl->id)
            ->update($data);

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Bloque actualizado');
    }

    public function moveBlock(int $blockId, string $direction): void
    {
        $tpl = $this->selectedTemplate();

        $blocks = $tpl->blocks()->orderBy('sort_order')->get();
        $idx = $blocks->search(fn($b) => $b->id === $blockId);

        if ($idx === false) return;

        $swapWith = $direction === 'up' ? $idx - 1 : $idx + 1;
        if ($swapWith < 0 || $swapWith >= $blocks->count()) return;

        $a = $blocks[$idx];
        $b = $blocks[$swapWith];

        DB::transaction(function () use ($a, $b) {
            $tmp = $a->sort_order;
            $a->sort_order = $b->sort_order;
            $b->sort_order = $tmp;
            $a->save();
            $b->save();
        });

        // (opcional) no afecta al orden por día, pero refrescamos por consistencia
        $this->loadWeekBlocks();

        $this->dispatch('toast', message: 'Orden actualizado');
    }

    private function selectedTemplate(): Template
    {
        return Template::query()
            ->where('user_id', Auth::id())
            ->with(['blocks.blockType'])
            ->findOrFail($this->selectedTemplateId);
    }

    /**
     * NUEVO: carga el panel "Semana" (bloques asignados por día).
     */
    protected function loadWeekBlocks(): void
    {
        foreach (range(1, 7) as $d) {
            $this->weekBlocks[$d] = [];
        }

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
                'title'    => (string) $r->title,
                'minutes'  => (int) ($r->estimated_minutes ?? 0),
                'type'     => (string) ($r->type_name ?? ''),
                'color'    => (string) ($r->type_color ?? '#10B981'),
                'position' => (int) $r->position,
            ];
        }
    }

    /**
     * NUEVO: asigna un bloque a un día de la semana (en la plantilla seleccionada).
     */
    public function assignBlockToDay(int $blockId, int $weekday): void
    {
        if (!$this->selectedTemplateId) return;

        $weekday = max(1, min(7, $weekday));

        $exists = DB::table('template_block_weekday')
            ->where('template_id', $this->selectedTemplateId)
            ->where('template_block_id', $blockId)
            ->where('weekday', $weekday)
            ->exists();

        if ($exists) {
            $this->dispatch('toast', message: 'Ese bloque ya está en ese día');
            return;
        }

        $maxPos = (int) DB::table('template_block_weekday')
            ->where('template_id', $this->selectedTemplateId)
            ->where('weekday', $weekday)
            ->max('position');

        DB::table('template_block_weekday')->insert([
            'template_id'       => $this->selectedTemplateId,
            'template_block_id' => $blockId,
            'weekday'           => $weekday,
            'position'          => $maxPos + 1,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $this->loadWeekBlocks();
        $this->dispatch('toast', message: 'Asignado');
    }

    /**
     * NUEVO: quita una asignación (fila de pivote).
     */
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

        $types = BlockType::query()
            ->where('user_id', $userId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id','name','color']);

        $selected = null;
        $blocks = collect();

        if ($this->selectedTemplateId) {
            $selected = Template::query()
                ->where('user_id', $userId)
                ->with('blocks.blockType')
                ->find($this->selectedTemplateId);

            $blocks = $selected?->blocks?->sortBy('sort_order') ?? collect();

            foreach ($blocks as $b) {
                $this->editingTitle[$b->id] ??= $b->title;
                $this->editingMinutes[$b->id] ??= $b->estimated_minutes;
            }
        }

        return view('livewire.templates-manager', [
            'templates' => $templates,
            'types' => $types,
            'selected' => $selected,
            'blocks' => $blocks,
            // weekBlocks y activeWeekday ya viven como props públicas
        ])->layout('layouts.app');
    }

    /**
     * (Legacy) Si aún quieres guardar "días en los que aplica la plantilla"
     * esto sigue funcionando. Si ya no lo usas en la vista, lo puedes borrar luego.
     */
    public function saveWeekdays(): void
    {
        $tpl = $this->selectedTemplate();

        $days = collect($this->selectedWeekdays)
            ->map(fn($d) => (int) $d)
            ->filter(fn($d) => $d >= 1 && $d <= 7)
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($tpl, $days) {
            TemplateWeekday::where('template_id', $tpl->id)->delete();

            foreach ($days as $d) {
                TemplateWeekday::create([
                    'template_id' => $tpl->id,
                    'weekday' => $d,
                ]);
            }
        });

        $this->dispatch('toast', message: 'Días guardados');
    }
}
