<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-end justify-between mb-6">
        <div>
            <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>
            <div class="text-sm text-gray-500 mt-1">Bloques</div>
        </div>

        <a href="{{ route('foco.assign') }}"
           class="rounded-xl px-3 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
            Asignación semanal
        </a>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-6 gap-3 sm:items-end">
            <div class="sm:col-span-3">
                <label class="block text-xs text-gray-600 mb-1">Plantilla</label>
                <select class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                        wire:change="selectTemplate($event.target.value)">
                    @foreach($templates as $t)
                        <option value="{{ $t->id }}" @selected($selected?->id === $t->id)>
                            {{ $t->name }}@if($t->is_default) (Default)@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs text-gray-600 mb-1">Nombre plantilla</label>
                <input type="text" wire:model.defer="templateName"
                       class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
            </div>

            <div class="sm:col-span-6 flex justify-end gap-2">
                <button wire:click="createTemplate"
                        class="rounded-xl px-4 py-2 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                    + Crear plantilla
                </button>
                @if($selected)
                    <button wire:click="saveTemplateName"
                            class="rounded-xl px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
                        Guardar nombre
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($selected)
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4 mb-4">
            <div class="text-sm font-semibold text-gray-900 mb-3">Crear bloque</div>

            <div class="grid grid-cols-1 sm:grid-cols-6 gap-3 sm:items-end">
                <div class="sm:col-span-3">
                    <label class="block text-xs text-gray-600 mb-1">Título</label>
                    <input type="text" wire:model.defer="blockTitle"
                           class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                           placeholder="Ej: Trabajo profundo" />
                    @error('blockTitle') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-600 mb-1">Tipo</label>
                    <select wire:model.defer="blockTypeId"
                            class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                        <option value="">Selecciona…</option>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    @error('blockTypeId') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="sm:col-span-1">
                    <label class="block text-xs text-gray-600 mb-1">Min</label>
                    <input type="number" min="1" wire:model.defer="blockEstimated"
                           class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                           placeholder="—" />
                </div>

                <div class="sm:col-span-6 flex justify-end">
                    <button wire:click="addBlock"
                            class="rounded-xl px-4 py-2 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                        + Crear
                    </button>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 overflow-hidden bg-white shadow-sm">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 text-sm font-semibold text-gray-900">
                Bloques (biblioteca)
            </div>

            <ul class="divide-y divide-gray-200">
                @forelse($blocks as $b)
                    <li class="p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-block h-2.5 w-2.5 rounded-full" style="background-color: {{ $b->blockType->color ?? '#10B981' }}"></span>
                                    <span class="text-xs text-gray-500">{{ $b->blockType->name ?? '' }}</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-6 gap-2 sm:items-end">
                                    <div class="sm:col-span-4">
                                        <label class="block text-xs text-gray-600 mb-1">Título</label>
                                        <input type="text" wire:model.defer="editingTitle.{{ $b->id }}"
                                               class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs text-gray-600 mb-1">Min</label>
                                        <input type="number" min="1" wire:model.defer="editingMinutes.{{ $b->id }}"
                                               class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                                    </div>

                                    <div class="sm:col-span-6 flex justify-end gap-2 pt-1">
                                        <button wire:click="saveBlockEdits({{ $b->id }})"
                                                class="rounded-xl px-3 py-1.5 text-xs font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
                                            Guardar
                                        </button>
                                        <button wire:click="deleteBlock({{ $b->id }})"
                                                class="rounded-xl px-3 py-1.5 text-xs font-semibold bg-gray-100 text-red-700 hover:bg-gray-200">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 pt-6">
                                <button wire:click="moveBlock({{ $b->id }}, 'up')"
                                        class="rounded-xl px-2 py-1 text-xs bg-gray-100 text-gray-900 hover:bg-gray-200">↑</button>
                                <button wire:click="moveBlock({{ $b->id }}, 'down')"
                                        class="rounded-xl px-2 py-1 text-xs bg-gray-100 text-gray-900 hover:bg-gray-200">↓</button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="p-4 text-sm text-gray-500">Aún no hay bloques.</li>
                @endforelse
            </ul>
        </div>
    @endif

    <!-- Tiny toast -->
    <div x-data="{ show:false, message:'' }"
         x-on:toast.window="message=$event.detail.message; show=true; setTimeout(()=>show=false, 1800)"
         x-show="show"
         x-transition.opacity
         class="fixed bottom-6 right-6 rounded-xl bg-white border border-gray-200 shadow-sm px-4 py-2 text-sm text-gray-900"
         style="display:none;">
        <span x-text="message"></span>
    </div>
</div>
