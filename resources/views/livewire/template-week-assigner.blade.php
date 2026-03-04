{{-- resources/views/livewire/week-assigner.blade.php --}}
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-end justify-between mb-6">
        <div>
            <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>
            <div class="text-sm text-gray-500 mt-1">Semana</div>
        </div>

        <a href="{{ route('foco.library') }}"
           class="rounded-xl px-3 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
            Biblioteca
        </a>
    </div>

    @php
        $days = [
            1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue',
            5 => 'Vie', 6 => 'Sáb', 7 => 'Dom',
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sidebar: días -->
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
            </div>
        </div>

        <!-- Main -->
        <div class="lg:col-span-8">
            <!-- Asignados -->
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm font-semibold text-gray-900">
                        Bloques asignados a {{ $days[$activeWeekday] }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ count($weekBlocks[$activeWeekday] ?? []) }}
                    </div>
                </div>

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
                                    wire:click="unassign({{ $wb['id'] }})"
                                    class="shrink-0 rounded-xl px-3 py-1.5 text-xs font-semibold bg-gray-100 text-red-700 hover:bg-gray-200">
                                Quitar
                            </button>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">No hay bloques asignados aún.</div>
                    @endforelse
                </div>
            </div>

            <!-- Biblioteca para asignar -->
            <div class="rounded-2xl border border-gray-200 overflow-hidden bg-white shadow-sm">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 text-sm font-semibold text-gray-900">
                    Biblioteca (asignar a {{ $days[$activeWeekday] }})
                </div>

                <ul class="divide-y divide-gray-200">
                    @forelse($library as $b)
                        <li class="p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block h-2.5 w-2.5 rounded-full"
                                              style="background-color: {{ $b->blockType->color ?? '#10B981' }}"></span>
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $b->title }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $b->blockType->name ?? '' }}
                                        @if(!is_null($b->estimated_minutes)) · {{ $b->estimated_minutes }} min @endif
                                    </div>
                                </div>

                                <button type="button"
                                        wire:click="assign({{ $b->id }})"
                                        class="shrink-0 rounded-xl px-3 py-2 text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                                    Asignar
                                </button>
                            </div>
                        </li>
                    @empty
                        <li class="p-4 text-sm text-gray-500">
                            No hay bloques en la biblioteca. Ve a “Biblioteca” y crea alguno.
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
