<?php

namespace App\Livewire;

use App\Models\BlockType;
use App\Models\Template;
use App\Models\TemplateBlock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TemplateBlocksManager extends Component
{
    public ?int $selectedTemplateId = null;
    public string $templateName = '';

    public string $blockTitle = '';
    public ?int $blockTypeId = null;
    public ?int $blockEstimated = null;

    public array $editingMinutes = []; // [blockId => minutes]
    public array $editingTitle = [];   // [blockId => title]

    public function mount(): void
    {
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

        $first = $default ?: Template::query()
            ->where('user_id', $userId)
            ->orderBy('id')
            ->first();

        $this->selectedTemplateId = $first?->id;
        $this->templateName = $first?->name ?? '';
    }

    public function selectTemplate(int $id): void
    {
        $tpl = Template::query()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $this->selectedTemplateId = $tpl->id;
        $this->templateName = $tpl->name;

        $this->editingMinutes = [];
        $this->editingTitle = [];
    }

    private function selectedTemplate(): Template
    {
        return Template::query()
            ->where('user_id', Auth::id())
            ->with(['blocks.blockType'])
            ->findOrFail($this->selectedTemplateId);
    }

    public function createTemplate(): void
    {
        $this->validate(['templateName' => 'required|string|max:120']);

        $tpl = Template::create([
            'user_id' => Auth::id(),
            'name' => trim($this->templateName),
            'is_default' => false,
        ]);

        $this->selectedTemplateId = $tpl->id;
        $this->dispatch('toast', message: 'Plantilla creada');
    }

    public function saveTemplateName(): void
    {
        $this->validate(['templateName' => 'required|string|max:120']);

        $tpl = $this->selectedTemplate();
        $tpl->update(['name' => trim($this->templateName)]);

        $this->dispatch('toast', message: 'Nombre guardado');
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

        $this->dispatch('toast', message: 'Bloque creado');
    }

    public function saveBlockEdits(int $blockId): void
    {
        $tpl = $this->selectedTemplate();

        $title = $this->editingTitle[$blockId] ?? null;
        $mins  = $this->editingMinutes[$blockId] ?? null;

        $data = [];
        if (!is_null($title)) $data['title'] = trim((string) $title);
        if ($mins === '' || is_null($mins)) $data['estimated_minutes'] = null;
        else $data['estimated_minutes'] = (int) $mins;

        if (!$data) return;

        TemplateBlock::query()
            ->where('id', $blockId)
            ->where('template_id', $tpl->id)
            ->update($data);

        $this->dispatch('toast', message: 'Bloque actualizado');
    }

    public function deleteBlock(int $blockId): void
    {
        $tpl = $this->selectedTemplate();

        DB::transaction(function () use ($tpl, $blockId) {
            DB::table('template_block_weekday')
                ->where('template_id', $tpl->id)
                ->where('template_block_id', $blockId)
                ->delete();

            TemplateBlock::query()
                ->where('id', $blockId)
                ->where('template_id', $tpl->id)
                ->delete();
        });

        $this->dispatch('toast', message: 'Bloque eliminado');
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

        $this->dispatch('toast', message: 'Orden actualizado');
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

        return view('livewire.template-blocks-manager', compact('templates','types','selected','blocks'))
            ->layout('layouts.app');
    }
}
