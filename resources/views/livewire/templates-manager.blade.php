<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-end justify-between mb-6">
        <div>
            <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>
            <div class="text-sm text-gray-500 mt-1">Plantillas</div>
        </div>
    </div>

    @php
        $days = [
            1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue',
            5 => 'Vie', 6 => 'Sáb', 7 => 'Dom',
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sidebar: DÍAS -->
        <div class="lg:col-span-4">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <div class="text-sm font-medium text-gray-900 mb-3">Días</div>

                <div class="space-y-2">
                    @foreach($days as $k => $label)
                        @php $count = count($weekBlocks[$k] ?? []); @endphp
                        <button
                            type="button"
                            wire:click="$set('activeWeekday', {{ $k }})"
                            class="w-full text-left rounded-xl px-3 py-2 border
                                {{ $activeWeekday === $k ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white hover:bg-gray-50' }}"
                        >
                            <div class="flex items-center justify-between">
                                <div class="text-gray-900 font-medium">{{ $label }}</div>
                                <span class="text-xs text-gray-500">{{ $count }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>

                <!-- Plantillas + Crear/Renombrar (se queda en sidebar para rapidez) -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="text-sm font-medium text-gray-900 mb-2">Plantilla</div>

                    <select
                        class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                        wire:change="selectTemplate($event.target.value)"
                    >
                        @foreach($templates as $t)
                            <option value="{{ $t->id }}" @selected($selected?->id === $t->id)>
                                {{ $t->name }}@if($t->is_default) (Default)@endif
                            </option>
                        @endforeach
                    </select>

                    <label class="block text-xs text-gray-600 mb-1 mt-3">Nombre plantilla</label>
                    <input type="text"
                           wire:model.defer="templateName"
                           class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                           placeholder="Ej: Semana base" />
                    @error('templateName')
                        <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                    @enderror

                    <div class="mt-3 flex gap-2">
                        <button wire:click="createTemplate"
                                class="flex-1 rounded-xl px-3 py-2 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                            + Crear
                        </button>
                        @if($selected)
                            <button wire:click="saveTemplateName"
                                    class="flex-1 rounded-xl px-3 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
                                Guardar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main: CONFIGURACIÓN DEL DÍA -->
        <div class="lg:col-span-8">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                @if(!$selected)
                    <div class="text-gray-700">
                        <div class="font-semibold text-gray-900 mb-1">Crea tu primera plantilla</div>
                        <div class="text-sm text-gray-600">
                            Cuando marques una como predeterminada, FOCO se rellenará solo cada mañana.
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="text-gray-900 font-semibold">
                                {{ $selected->name }}
                                <span class="text-gray-400 font-normal">·</span>
                                <span class="text-emerald-700">{{ $days[$activeWeekday] }}</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                Bloques en plantilla: {{ $blocks->count() }} · Asignados hoy: {{ count($weekBlocks[$activeWeekday] ?? []) }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- <button wire:click="setAsDefault"
                                    class="rounded-xl px-3 py-2 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                                Marcar default
                            </button> --}}

                            <button wire:click="deleteSelectedTemplate"
                                    class="rounded-xl px-3 py-2 text-sm font-semibold bg-gray-100 text-red-700 hover:bg-gray-200">
                                Eliminar
                            </button>
                        </div>
                    </div>

                    <!-- Bloques asignados al día -->
                    <div class="rounded-2xl border border-gray-200 bg-white p-4 mb-4">
                        <div class="text-sm font-semibold text-gray-900 mb-2">Bloques asignados a {{ $days[$activeWeekday] }}</div>

                        <div class="space-y-2">
                            @forelse(($weekBlocks[$activeWeekday] ?? []) as $wb)
                                <div class="rounded-xl border border-gray-200 bg-white p-3 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block h-2.5 w-2.5 rounded-full" style="background-color: {{ $wb['color'] }}"></span>
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $wb['title'] }}</div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $wb['type'] }} @if($wb['type']) · @endif {{ $wb['minutes'] }} min
                                        </div>
                                    </div>

                                    <button type="button"
                                            wire:click="unassignPivot({{ $wb['pivot_id'] }})"
                                            class="shrink-0 rounded-xl px-3 py-1.5 text-xs font-semibold bg-gray-100 text-red-700 hover:bg-gray-200">
                                        Quitar
                                    </button>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500">Aún no has asignado bloques a este día.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- <a href="{{ route('foco.blocks') }}"
                        class="rounded-xl px-3 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
                        Gestionar bloques
                    </a> --}}

                    <!-- Blocks list -->
                    <div class="rounded-2xl border border-gray-200 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 text-sm font-semibold text-gray-900">
                            Bloques de la plantilla (disponibles para asignar)
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
                                                    <input type="text"
                                                           wire:model.defer="editingTitle.{{ $b->id }}"
                                                           class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                                                </div>
                                                <div class="sm:col-span-2">
                                                    <label class="block text-xs text-gray-600 mb-1">Min</label>
                                                    <input type="number" min="1"
                                                           wire:model.defer="editingMinutes.{{ $b->id }}"
                                                           class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                                                </div>

                                                <div class="sm:col-span-6 flex justify-end gap-2 pt-1">
                                                    {{-- <button wire:click="saveBlockEdits({{ $b->id }})"
                                                            class="rounded-xl px-3 py-1.5 text-xs font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
                                                        Guardar
                                                    </button> --}}

                                                    <button type="button"
                                                            wire:click="assignBlockToDay({{ $b->id }}, {{ $activeWeekday }})"
                                                            class="rounded-xl px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                                                        Asignar a {{ $days[$activeWeekday] }}
                                                    </button>

                                                    {{-- <button wire:click="deleteBlock({{ $b->id }})"
                                                            class="rounded-xl px-3 py-1.5 text-xs font-semibold bg-gray-100 text-red-700 hover:bg-gray-200">
                                                        Eliminar
                                                    </button> --}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col gap-2 pt-6">
                                            <button wire:click="moveBlock({{ $b->id }}, 'up')"
                                                    class="rounded-xl px-2 py-1 text-xs bg-gray-100 text-gray-900 hover:bg-gray-200">
                                                ↑
                                            </button>
                                            <button wire:click="moveBlock({{ $b->id }}, 'down')"
                                                    class="rounded-xl px-2 py-1 text-xs bg-gray-100 text-gray-900 hover:bg-gray-200">
                                                ↓
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="p-4 text-sm text-gray-500">Aún no hay bloques. Añade el primero arriba.</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="mt-4 text-xs text-gray-500">
                        Consejo: selecciona un día a la izquierda y asigna bloques desde la lista de abajo.
                    </div>
                @endif
            </div>

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
    </div>
</div>
