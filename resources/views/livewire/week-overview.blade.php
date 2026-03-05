<div class="min-h-[calc(100vh-4rem)] bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-6">

        {{-- HEADER --}}
        <div class="flex items-end justify-between mb-6">
            <div>
                <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>
                <div class="text-sm text-gray-500 mt-1">
                    Vista semanal · <span class="font-medium text-gray-700">Semana tipo</span>
                    <span class="ml-2 text-xs text-gray-400">solo lectura</span>
                </div>
            </div>
        </div>


        {{-- LISTA VERTICAL DE DÍAS --}}
        <div class="space-y-4">

            @foreach($days as $d)
                @php($weekday = (int) $d['weekday'])

                <button type="button"
                        wire:click="goToDay({{ $weekday }})"
                        class="w-full text-left rounded-2xl border border-gray-200 bg-white shadow-sm p-4
                               hover:border-emerald-200 hover:shadow transition">

                    {{-- HEADER DIA --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $d['label'] }}
                        </div>

                        <span class="text-xs text-gray-400">
                            Editar →
                        </span>
                    </div>


                    {{-- BLOQUES --}}
                    <div class="space-y-2">

                        @forelse(($byWeekday[$weekday] ?? []) as $b)

                            <div class="rounded-xl border border-gray-200 p-3">

                                <div class="flex items-center justify-between gap-3">

                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $b->title }}
                                    </div>

                                    @if(!empty($b->start_time))
                                        <div class="text-xs font-semibold text-gray-600 whitespace-nowrap">
                                            {{ substr((string) $b->start_time,0,5) }}
                                        </div>
                                    @endif

                                </div>


                                {{-- METADATA --}}
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-500">

                                    @if(!empty($b->type_name))
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5">
                                            {{ $b->type_name }}
                                        </span>
                                    @endif


                                    @if(!empty($b->estimated_minutes))
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5">
                                            {{ $b->estimated_minutes }} min
                                        </span>
                                    @endif


                                    @php($p = $b->priority ?? null)

                                    @if($p === 'urgent')
                                        <span class="rounded-full bg-amber-100 text-amber-800 px-2 py-0.5 font-semibold">
                                            Urgente
                                        </span>
                                    @elseif($p === 'high')
                                        <span class="rounded-full bg-red-100 text-red-800 px-2 py-0.5 font-semibold">
                                            Muy prioritario
                                        </span>
                                    @elseif($p === 'none' || $p === '' || is_null($p))
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5">
                                            Sin prioridad
                                        </span>
                                    @endif

                                </div>

                            </div>

                        @empty

                            <div class="text-sm text-gray-400 italic">
                                Sin bloques
                            </div>

                        @endforelse

                    </div>

                </button>

            @endforeach

        </div>

    </div>
</div>
