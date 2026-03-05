<?php
// app/Livewire/LibraryBlocksManager.php

namespace App\Livewire;

use App\Models\BlockType;
use App\Models\LibraryBlock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LibraryBlocksManager extends Component
{
    /** Form crear */
    public string $blockTitle = '';
    public ?int $blockTypeId = null;
    public ?int $blockEstimated = null;

    /** ✅ Nuevo: notas/explicación del bloque (solo biblioteca) */
    public ?string $blockNotes = null;

    /** Edición inline */
    public array $editingTitle = [];   // [id => title]
    public array $editingMinutes = []; // [id => minutes]
    public array $editingNotes = [];   // [id => notes]
    public array $editingTypeId = []; // [id => block_type_id]

    public array $openNotes = []; // [id => bool]

    public function toggleNotes(int $id): void
    {
        $this->openNotes[$id] = !($this->openNotes[$id] ?? false);
    }
    /**
     * Crea un bloque en la biblioteca.
     * - Este bloque luego se asigna a días en "Semana tipo".
     * - La nota es global del bloque (rutina, explicación, etc.).
     */
    public function createLibraryBlock(): void
    {
        $this->validate([
            'blockTitle' => 'required|string|max:180',
            'blockTypeId' => 'required|integer',
            'blockEstimated' => 'nullable|integer|min:1|max:999',
            'blockNotes' => 'nullable|string',
        ]);

        $notes = trim((string) $this->blockNotes);
        if ($notes === '') $notes = null;

        LibraryBlock::create([
            'user_id' => Auth::id(),
            'block_type_id' => $this->blockTypeId,
            'title' => trim($this->blockTitle),
            'estimated_minutes' => $this->blockEstimated,
            'notes' => $notes, // ✅
        ]);

        // Reset del formulario
        $this->blockTitle = '';
        $this->blockEstimated = null;
        $this->blockNotes = null;
        $this->primeEditingState();
        $this->dispatch('toast', message: 'Bloque creado');
    }

    /**
     * Guarda cambios de un bloque de biblioteca (edición inline).
     * Campos editables:
     * - title
     * - estimated_minutes
     * - notes
     */
    public function saveLibraryBlock(int $id): void
    {
        $userId = Auth::id();

        $lb = LibraryBlock::query()
            ->where('user_id', $userId)
            ->findOrFail($id);

        $title = $this->editingTitle[$id] ?? null;
        $mins  = $this->editingMinutes[$id] ?? null;
        $notes = $this->editingNotes[$id] ?? null;

        logger()->info('SAVE NOTES', ['title' => $title,'min' => $mins,'notes' => $notes]);


        $data = [];

        if (!is_null($title)) {
            $data['title'] = trim((string) $title);
        }

        if ($mins === '' || is_null($mins)) {
            $data['estimated_minutes'] = null;
        } else {
            $data['estimated_minutes'] = (int) $mins;
        }

        // ✅ notas (puede ser null)
        if (!is_null($notes)) {
            $v = trim((string) $notes);
            $data['notes'] = $v === '' ? null : $v;
        }

        $typeId = $this->editingTypeId[$id] ?? null;
        if (!is_null($typeId)) $data['block_type_id'] = (int) $typeId;

        logger()->info('SAVE NOTES', $data);

        // if (!$data) return;
        // logger()->info('SAVE NOTES', ['id'=>$id, 'notes'=>$notes]);
        $lb->update($data);

        // mantener state alineado (por si trim convierte a null)
        // $this->editingNotes[$id] = (string) ($lb->fresh()->notes ?? '');


        $this->dispatch('toast', message: 'Bloque actualizado');
    }

    /**
     * Elimina un bloque de biblioteca.
     * - También elimina sus asignaciones en weekday_blocks (Semana tipo).
     * - Los focus_blocks del histórico NO se tocan (si tienes FK nullOnDelete, se quedará null).
     */
    public function deleteLibraryBlock(int $id): void
    {
        $userId = Auth::id();

        DB::transaction(function () use ($userId, $id) {
            // Quitar asignaciones semanales de ese bloque
            DB::table('weekday_blocks')
                ->where('user_id', $userId)
                ->where('library_block_id', $id)
                ->delete();

            // Borrar bloque de biblioteca
            LibraryBlock::query()
                ->where('user_id', $userId)
                ->where('id', $id)
                ->delete();
        });

        unset($this->editingTitle[$id], $this->editingMinutes[$id], $this->editingNotes[$id]);
        $this->primeEditingState();
        $this->dispatch('toast', message: 'Bloque eliminado');
    }

    public function render()
    {
        $userId = Auth::id();

        $types = BlockType::query()
            ->where('user_id', $userId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id','name','color']);

        $blocks = LibraryBlock::query()
            ->where('user_id', $userId)
            ->with('blockType')
            ->orderBy('id', 'desc')
            ->get();

        // Precarga campos de edición (solo si aún no están en el array)
        // foreach ($blocks as $b) {
        //     $this->editingTitle[$b->id] ??= $b->title;
        //     $this->editingMinutes[$b->id] ??= $b->estimated_minutes;
        //     $this->editingNotes[$b->id] ??= $b->notes;
        //     $this->editingTypeId[$b->id] ??= $b->block_type_id;
        // }

        return view('livewire.library-blocks-manager', [
            'types' => $types,
            'blocks' => $blocks,
        ])->layout('layouts.app');
    }

    public function mount(): void
    {
        $this->primeEditingState();
    }

    private function primeEditingState(): void
    {
        $userId = Auth::id();

        $blocks = LibraryBlock::query()
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get(['id','title','estimated_minutes','notes','block_type_id']);

        foreach ($blocks as $b) {
    $id = (int) $b->id;

    $this->editingTitle[$id]   ??= $b->title ?? '';
    $this->editingMinutes[$id] ??= $b->estimated_minutes;
    $this->editingTypeId[$id]  ??= $b->block_type_id;

    if (!array_key_exists($id, $this->editingNotes)) {
        $this->editingNotes[$id] = (string) ($b->notes ?? '');
    }

    if (!array_key_exists($id, $this->openNotes)) {
        $this->openNotes[$id] = !empty($this->editingNotes[$id]);
    }
}
    }
}
