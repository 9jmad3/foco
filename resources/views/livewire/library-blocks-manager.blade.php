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
                       placeholder="Ej: Piernas (rutina)"
                       class="w-full rounded-xl border border-gray-300 text-gray-900 placeholder:text-gray-400
                              focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                @error('blockTitle')
                    <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Tipo</label>
                <select wire:model.defer="blockTypeId"
                        class="w-full rounded-xl border border-gray-300 text-gray-900
                               focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                    <option value="">Tipo…</option>
                    @foreach($types as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
                @error('blockTypeId')
                    <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="sm:col-span-1">
                <label class="block text-xs text-gray-600 mb-1">Min</label>
                <input type="number"
                       min="1"
                       wire:model.defer="blockEstimated"
                       placeholder="—"
                       class="w-full rounded-xl border border-gray-300 text-gray-900 placeholder:text-gray-400
                              focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                @error('blockEstimated')
                    <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- ✅ Detalles / notas (colapsado) --}}
            <div class="sm:col-span-6" x-data="{ open: false }">
                <div class="flex items-center justify-between">
                    <label class="block text-xs text-gray-600">Detalles (opcional)</label>

                    <button type="button"
                            x-on:click="open = !open"
                            class="text-xs text-gray-600 hover:text-gray-900 underline underline-offset-4">
                        <span x-show="!open">Añadir detalles</span>
                        <span x-show="open" style="display:none;">Ocultar</span>
                    </button>
                </div>

                <div x-show="open" x-transition.opacity class="mt-2" style="display:none;">
                    <textarea rows="4"
                            wire:model.defer="blockNotes"
                            placeholder=""
                            class="w-full rounded-xl border border-gray-300 text-gray-900 placeholder:text-gray-400
                                    focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"></textarea>

                    @error('blockNotes')
                        <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                    @enderror

                    <div class="mt-1 text-[11px] text-gray-500">
                        Esto se verá en “Hoy” al desplegar el bloque.
                    </div>
                </div>
            </div>

            <div class="sm:col-span-6 flex justify-end">
                <button wire:click="createLibraryBlock"
                        class="rounded-xl px-4 py-2 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                    Crear
                </button>
            </div>
        </div>
    </div>

    <!-- Listado -->
    <div class="space-y-3">
        @forelse($blocks as $b)
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                {{-- HEADER --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-2.5 w-2.5 rounded-full"
                                style="background-color: {{ $b->blockType->color ?? '#10B981' }}"></span>

                            <div class="text-sm font-semibold text-gray-900 truncate">
                                {{ $b->title }}
                            </div>
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            {{ $b->blockType->name ?? '' }}
                            @if($b->estimated_minutes) · {{ $b->estimated_minutes }} min @endif
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                        <button wire:click="saveLibraryBlock({{ $b->id }})"
                                class="rounded-xl px-3 py-1.5 text-xs font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
                            Guardar
                        </button>

                        <button wire:click="deleteLibraryBlock({{ $b->id }})"
                                wire:confirm="¿Eliminar este bloque?"
                                class="rounded-xl px-3 py-1.5 text-xs font-semibold bg-gray-100 text-red-700 hover:bg-gray-200">
                            Eliminar
                        </button>
                    </div>
                </div>

                {{-- SEPARADOR SUAVE --}}
                <div class="my-4 border-t border-gray-100"></div>

                {{-- FORM --}}
                <div class="grid grid-cols-1 sm:grid-cols-6 gap-3 sm:items-end">
                    <div class="sm:col-span-3">
                        <label class="block text-xs text-gray-600 mb-1">Título</label>
                        <input type="text"
                            wire:model.defer="editingTitle.{{ $b->id }}"
                            class="w-full rounded-xl border border-gray-300 text-gray-900
                                    focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs text-gray-600 mb-1">Tipo</label>
                        <select wire:model.defer="editingTypeId.{{ $b->id }}"
                                class="w-full rounded-xl border border-gray-300 text-gray-900
                                    focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                            @foreach($types as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label class="block text-xs text-gray-600 mb-1">Min</label>
                        <input type="number" min="1"
                            wire:model.defer="editingMinutes.{{ $b->id }}"
                            class="w-full rounded-xl border border-gray-300 text-gray-900
                                    focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                    </div>

                    {{-- AQUÍ VA “Detalles colapsado” --}}
                    <div class="sm:col-span-6"
                        x-data="{ open: false }">

                        <div class="flex items-center justify-between">
                            <label class="block text-xs text-gray-600">Detalles (opcional)</label>

                            <button type="button"
                                    x-on:click="open = !open"
                                    class="text-xs text-gray-600 hover:text-gray-900 underline underline-offset-4">
                                <span x-show="!open">Añadir detalles</span>
                                <span x-show="open" style="display:none;">Ocultar</span>
                            </button>
                        </div>

                        <div x-show="open" x-transition.opacity class="mt-2" style="display:none;">
                            <textarea rows="4"
                                    wire:model.defer="editingNotes.{{ $b->id }}"
                                    placeholder="Rutina / instrucciones…"
                                    class="w-full rounded-xl border border-gray-300 text-gray-900 placeholder:text-gray-400
                                            focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4 text-sm text-gray-500">
                No hay bloques aún.
            </div>
        @endforelse
    </div>
</div>
