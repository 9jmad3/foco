<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-end justify-between mb-6">
        <div>
            <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>
            <div class="text-sm text-gray-500 mt-1">Semana tipo</div>
        </div>

        <a href="{{ route('foco.library') }}"
           class="rounded-xl px-3 py-2 text-sm font-semibold bg-gray-200 text-gray-900 hover:bg-gray-300">
            Biblioteca
        </a>
    </div>

    @php
        $days = [
            1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue',
            5 => 'Vie', 6 => 'Sáb', 7 => 'Dom',
        ];

        // IDs de LibraryBlock ya asignados al día activo
        $assignedIds = collect($weekBlocks[$activeWeekday] ?? [])
            ->pluck('library_block_id')
            ->filter()
            ->map(fn($v) => (int) $v)
            ->values()
            ->all();

        $assignedSet = array_flip($assignedIds);

        // Biblioteca disponible (excluye asignados)
        $availableLibrary = $library->filter(function ($b) use ($assignedSet) {
            return !isset($assignedSet[(int) $b->id]);
        });

        /**
         * ✅ Ordenación visual de los asignados:
         * - Primero los que tengan hora (start_time)
         * - Orden por start_time asc (HH:MM)
         * - Los NULL al final
         * - Fallback por position
         */
        $sortedAssigned = collect($weekBlocks[$activeWeekday] ?? [])->sort(function ($a, $b) {
            $aTime = !empty($a['start_time']) ? substr((string)$a['start_time'], 0, 5) : null;
            $bTime = !empty($b['start_time']) ? substr((string)$b['start_time'], 0, 5) : null;

            // nulls last
            if ($aTime === null && $bTime !== null) return 1;
            if ($aTime !== null && $bTime === null) return -1;

            // both have time -> compare
            if ($aTime !== null && $bTime !== null) {
                if ($aTime < $bTime) return -1;
                if ($aTime > $bTime) return 1;
            }

            // fallback por position (o 0 si no existe)
            $ap = (int)($a['position'] ?? 0);
            $bp = (int)($b['position'] ?? 0);
            return $ap <=> $bp;
        })->values();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sidebar días -->
        <div class="lg:col-span-4">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
                <div class="text-sm font-medium text-gray-900 mb-3">Días</div>

                <div class="space-y-2">
                    @foreach($days as $k => $label)
                        <button
                            type="button"
                            wire:click="$set('activeWeekday', {{ $k }})"
                            class="w-full text-left rounded-xl px-3 py-2 border
                                {{ $activeWeekday === $k ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white hover:bg-gray-50' }}"
                        >
                            <div class="flex items-center justify-between">
                                <div class="text-gray-900 font-medium">{{ $label }}</div>
                                <span class="text-xs text-gray-500">
                                    {{ count($weekBlocks[$k] ?? []) }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="lg:col-span-8">

            <!-- Bloques asignados -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4 mb-4">
                <div class="text-sm font-semibold text-gray-900 mb-3">
                    Bloques asignados a {{ $days[$activeWeekday] }}
                </div>

                <div class="space-y-2">
                    @forelse($sortedAssigned as $wb)
                        <div wire:key="weekday-block-row-{{ $wb['id'] }}"
                                    class="rounded-xl border border-gray-200 bg-white p-3 flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block h-2.5 w-2.5 rounded-full"
                                          style="background-color: {{ $wb['color'] }}"></span>
                                    <div class="text-sm font-medium text-gray-900 truncate">
                                        {{ $wb['title'] }}
                                    </div>
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $wb['type'] }}
                                    @if($wb['minutes'])
                                        · {{ $wb['minutes'] }} min
                                    @endif
                                </div>
                            </div>

                            {{-- Hora --}}
                            <div class="shrink-0 flex items-center gap-2">
                                <span class="text-xs text-gray-500">Hora</span>

                                <input
                                    wire:key="weekday-block-time-{{ $wb['id'] }}"
                                    type="time"
                                    value="{{ !empty($wb['start_time']) ? substr($wb['start_time'], 0, 5) : '' }}"
                                    class="rounded-lg border border-gray-300 px-2 py-1 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                                    wire:change="updateStartTime({{ $wb['id'] }}, $event.target.value)"
                                />
                            </div>

                            {{-- Prioridad --}}
                            <div class="shrink-0 flex items-center gap-2">
                                <span class="text-xs text-gray-500">Prioridad</span>

                                <select
                                    wire:key="weekday-block-priority-{{ $wb['id'] }}"
                                    class="rounded-lg border border-gray-300 px-2 py-1 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                                    wire:change="updatePriority({{ $wb['id'] }}, $event.target.value)"
                                >
                                    <option value="no_importante" {{ ($wb['priority'] ?? 'no_importante') === 'no_importante' ? 'selected' : '' }}>
                                        No importante
                                    </option>
                                    <option value="importante" {{ ($wb['priority'] ?? '') === 'importante' ? 'selected' : '' }}>
                                        Importante
                                    </option>
                                    <option value="urgente" {{ ($wb['priority'] ?? '') === 'urgente' ? 'selected' : '' }}>
                                        Urgente
                                    </option>
                                </select>
                            </div>

                            <button
                                wire:click="unassign({{ $wb['id'] }})"
                                class="shrink-0 rounded-xl px-3 py-1.5 text-xs font-semibold bg-gray-100 text-red-700 hover:bg-gray-200">
                                Quitar
                            </button>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">
                            No hay bloques asignados aún.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Biblioteca disponible (solo NO asignados) -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 text-sm font-semibold text-gray-900">
                    Bloques disponibles para {{ $days[$activeWeekday] }}
                </div>

                <ul class="divide-y divide-gray-200">
                    @forelse($availableLibrary as $b)
                        <li class="p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block h-2.5 w-2.5 rounded-full"
                                              style="background-color: {{ $b->blockType->color ?? '#10B981' }}"></span>
                                        <div class="text-sm font-medium text-gray-900 truncate">
                                            {{ $b->title }}
                                        </div>
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $b->blockType->name ?? '' }}
                                        @if($b->estimated_minutes)
                                            · {{ $b->estimated_minutes }} min
                                        @endif
                                    </div>
                                </div>

                                <button
                                    wire:click="assign({{ $b->id }})"
                                    class="shrink-0 rounded-xl px-3 py-2 text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                                    Asignar
                                </button>
                            </div>
                        </li>
                    @empty
                        <li class="p-4 text-sm text-gray-500">
                            No hay bloques disponibles (ya tienes todos asignados a este día).
                        </li>
                    @endforelse
                </ul>
            </div>

        </div>
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
