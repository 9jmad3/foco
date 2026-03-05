<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-end justify-between mb-6">
        <div>
            <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>
            <div class="text-sm text-gray-500 mt-1">Biblioteca</div>
        </div>

        <a href="{{ route('foco.week') }}"
           class="rounded-xl px-3 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
            Semana
        </a>
    </div>


    <!-- Crear bloque -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4 mb-4">
        <div class="text-sm font-semibold text-gray-900 mb-3">
            Crear bloque
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-6 gap-3 sm:items-end">

            <div class="sm:col-span-3">
                <label class="block text-xs text-gray-600 mb-1">Título</label>
                <input type="text"
                       wire:model.defer="blockTitle"
                       class="w-full rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-400">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Tipo</label>
                <select wire:model.defer="blockTypeId"
                        class="w-full rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-400">
                    <option value="">Tipo…</option>
                    @foreach ($types as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-1">
                <label class="block text-xs text-gray-600 mb-1">Min</label>
                <input type="number"
                       min="1"
                       wire:model.defer="blockEstimated"
                       class="w-full rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-400">
            </div>


            {{-- Notas creación --}}
            <div class="sm:col-span-6" x-data="{ open:false }">
                <div class="flex justify-between">
                    <label class="text-xs text-gray-600">Detalles</label>

                    <button type="button"
                            x-on:click="open=!open"
                            class="text-xs underline">
                        <span x-show="!open">Añadir detalles</span>
                        <span x-show="open">Ocultar</span>
                    </button>
                </div>

                <div x-show="open" class="mt-2">
                    <textarea rows="4"
                              wire:model.defer="blockNotes"
                              class="w-full rounded-xl border border-gray-300"></textarea>
                </div>
            </div>

            <div class="sm:col-span-6 flex justify-end">
                <button type="button"
                        wire:click="createLibraryBlock"
                        class="rounded-xl px-4 py-2 text-sm bg-emerald-600 text-white">
                    Crear
                </button>
            </div>

        </div>
    </div>


    <!-- LISTADO -->
    <div class="space-y-3">

        @forelse($blocks as $b)

        <form wire:submit.prevent="saveLibraryBlock({{ $b->id }})"
              wire:key="library-block-{{ $b->id }}"
              class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">

            {{-- HEADER --}}
            <div class="flex justify-between mb-4">

                <div>
                    <div class="font-semibold text-gray-900">
                        {{ $b->title }}
                    </div>

                    <div class="text-xs text-gray-500">
                        {{ $b->blockType->name ?? '' }}
                        @if($b->estimated_minutes)
                        · {{ $b->estimated_minutes }} min
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">

                    <button type="submit"
                            class="rounded-xl px-3 py-1.5 text-xs bg-gray-100 hover:bg-gray-200">
                        Guardar
                    </button>

                    <button type="button"
                            wire:click="deleteLibraryBlock({{ $b->id }})"
                            class="rounded-xl px-3 py-1.5 text-xs text-red-700 bg-gray-100 hover:bg-gray-200">
                        Eliminar
                    </button>

                </div>

            </div>


            {{-- FORM --}}
            <div class="grid grid-cols-1 sm:grid-cols-6 gap-3">

                <div class="sm:col-span-3">
                    <label class="text-xs text-gray-600">Título</label>
                    <input type="text"
                           wire:model.defer="editingTitle.{{ $b->id }}"
                           class="w-full rounded-xl border border-gray-300">
                </div>

                <div class="sm:col-span-2">
                    <label class="text-xs text-gray-600">Tipo</label>
                    <select wire:model.defer="editingTypeId.{{ $b->id }}"
                            class="w-full rounded-xl border border-gray-300">
                        @foreach ($types as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-1">
                    <label class="text-xs text-gray-600">Min</label>
                    <input type="number"
                           wire:model.defer="editingMinutes.{{ $b->id }}"
                           class="w-full rounded-xl border border-gray-300">
                </div>


                {{-- NOTAS --}}
                {{-- NOTAS (sin Alpine) --}}
<div class="sm:col-span-6">
    <div class="flex justify-between">
        <label class="text-xs text-gray-600">Detalles</label>

        <button type="button"
                wire:click="toggleNotes({{ $b->id }})"
                class="text-xs underline">
            @if(($openNotes[$b->id] ?? false) === false)
                {{ !empty($editingNotes[$b->id] ?? '') ? 'Ver detalles' : 'Añadir detalles' }}
            @else
                Ocultar
            @endif
        </button>
    </div>

    @if(!($openNotes[$b->id] ?? false) && !empty($editingNotes[$b->id] ?? ''))
        <div class="text-xs text-gray-500 mt-2 line-clamp-2">
            {{ $editingNotes[$b->id] }}
        </div>
    @endif

    @if(($openNotes[$b->id] ?? false))
        <div class="mt-2" wire:key="notes-wrap-{{ $b->id }}">
            <textarea rows="4"
                      wire:key="notes-{{ $b->id }}"
                      wire:model.defer="editingNotes.{{ $b->id }}"
                      placeholder="Rutina / instrucciones…"
                      class="w-full rounded-xl border border-gray-300 text-gray-900 placeholder:text-gray-400
                             focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"></textarea>
        </div>
    @endif
</div>

            </div>

        </form>

        @empty

        <div class="rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-500">
            No hay bloques aún.
        </div>

        @endforelse

    </div>
</div>
