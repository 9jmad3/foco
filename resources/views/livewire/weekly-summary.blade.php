<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-end justify-between mb-6">
        <div>
            <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>
            <div class="text-sm text-gray-500 mt-1">Resumen semanal</div>
        </div>

        {{-- <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('foco.today') }}" class="text-gray-600 hover:text-gray-900 underline underline-offset-4">Hoy</a>
            <a href="{{ route('foco.templates') }}" class="text-gray-600 hover:text-gray-900 underline underline-offset-4">Plantillas</a>
            <a href="{{ route('foco.settings') }}" class="text-gray-600 hover:text-gray-900 underline underline-offset-4">Ajustes</a>
        </div> --}}
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-gray-900 font-medium">
                Semana: <span class="text-gray-700">{{ $weekStart }}</span> → <span class="text-gray-700">{{ $weekEnd }}</span>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="prevWeek"
                        class="rounded-xl px-3 py-2 text-sm bg-gray-100 text-gray-900 hover:bg-gray-200">
                    ←
                </button>
                <button wire:click="nextWeek"
                        class="rounded-xl px-3 py-2 text-sm bg-gray-100 text-gray-900 hover:bg-gray-200">
                    →
                </button>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-4">
                <div class="text-xs text-gray-500">Total bloques</div>
                <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $total }}</div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4">
                <div class="text-xs text-gray-500">Completados</div>
                <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $completed }}</div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4">
                <div class="text-xs text-gray-500">Cumplimiento</div>
                <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $pct }}%</div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 text-sm font-medium text-gray-900">
            Por tipo
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-gray-500">
                    <tr class="border-b border-gray-200">
                        <th class="text-left font-medium px-6 py-3">Tipo</th>
                        <th class="text-right font-medium px-6 py-3">Completados</th>
                        <th class="text-right font-medium px-6 py-3">Total</th>
                        <th class="text-right font-medium px-6 py-3">Min est. completados</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700">
                    @forelse($byType as $row)
                        <tr class="border-b border-gray-200">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block h-2.5 w-2.5 rounded-full"
                                          style="background-color: {{ $row->color ?? '#10B981' }}"></span>
                                    <span class="text-gray-900 font-medium">{{ $row->type_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-right">{{ (int) $row->completed }}</td>
                            <td class="px-6 py-3 text-right">{{ (int) $row->total }}</td>
                            <td class="px-6 py-3 text-right">{{ (int) $row->est_minutes_completed }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-gray-500">
                                No hay datos esta semana.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
        <div class="text-sm font-semibold text-gray-900 mb-2">Clima emocional</div>

        @php
            $labels = [
                'calm' => '😌 Tranquilo',
                'good' => '🙂 Bien',
                'neutral' => '😐 Neutral',
                'tired' => '😩 Agotado',
                'frustrated' => '😤 Frustrado',
            ];
            $order = ['good','calm','neutral','tired','frustrated'];
        @endphp

        @if(($emotionTotal ?? 0) === 0)
            <div class="text-sm text-gray-500">
                Aún no hay emociones registradas esta semana.
            </div>
        @else
            <div class="space-y-2">
                @foreach($order as $k)
                    @php
                        $count = (int)($emotionCounts[$k] ?? 0);
                        $pct = $emotionTotal > 0 ? round(($count / $emotionTotal) * 100) : 0;
                    @endphp

                    @if($count > 0)
                        <div class="flex items-center gap-3">
                            <div class="w-40 text-sm text-gray-700">{{ $labels[$k] }}</div>

                            <div class="flex-1">
                                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-2 bg-emerald-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>

                            <div class="w-20 text-right text-sm text-gray-600">
                                {{ $pct }}% ({{ $count }})
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-3 text-xs text-gray-500">
                Cuenta solo bloques completados con emoción.
            </div>
        @endif
    </div>

    <div class="mt-4 rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
    <div class="text-sm font-semibold text-gray-900 mb-2">Cierre del día</div>

    @php
        $stateLabels = [
            'calm' => 'En paz',
            'satisfied' => 'Satisfecho',
            'tired' => 'Cansado',
            'tense' => 'Tenso',
        ];
        $stateOrder = ['satisfied','calm','tired','tense'];

        $rumLabels = [
            'none' => 'Sin vueltas',
            'controlled' => 'Controlado',
            'worried' => 'Me preocupa',
        ];
        $rumOrder = ['none','controlled','worried'];
    @endphp

    {{-- Estado final --}}
    <div class="text-xs font-semibold text-gray-700 mb-2">Cómo termina la semana</div>

    @if(($dayEndTotal ?? 0) === 0)
        <div class="text-sm text-gray-500">Aún no hay cierres registrados esta semana.</div>
    @else
        <div class="space-y-2">
            @foreach($stateOrder as $k)
                @php
                    $count = (int)($dayEndCounts[$k] ?? 0);
                    $pct = $dayEndTotal > 0 ? round(($count / $dayEndTotal) * 100) : 0;
                @endphp

                @if($count > 0)
                    <div class="flex items-center gap-3">
                        <div class="w-32 text-sm text-gray-700">{{ $stateLabels[$k] }}</div>
                        <div class="flex-1">
                            <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-2 bg-emerald-500" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        <div class="w-20 text-right text-sm text-gray-600">{{ $pct }}% ({{ $count }})</div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Rumiación --}}
    <div class="mt-5 text-xs font-semibold text-gray-700 mb-2">Rumiación</div>

        @if(($ruminationTotal ?? 0) === 0)
            <div class="text-sm text-gray-500">Aún no hay datos de rumiación esta semana.</div>
        @else
            <div class="space-y-2">
                @foreach($rumOrder as $k)
                    @php
                        $count = (int)($ruminationCounts[$k] ?? 0);
                        $pct = $ruminationTotal > 0 ? round(($count / $ruminationTotal) * 100) : 0;
                    @endphp

                    @if($count > 0)
                        <div class="flex items-center gap-3">
                            <div class="w-32 text-sm text-gray-700">{{ $rumLabels[$k] }}</div>
                            <div class="flex-1">
                                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-2 bg-emerald-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            <div class="w-20 text-right text-sm text-gray-600">{{ $pct }}% ({{ $count }})</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-4 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 text-sm font-semibold text-gray-900">
            Cierres por día
        </div>

        <div class="divide-y divide-gray-200">
            @foreach($closuresByDay as $d)
                <div class="px-4 py-3">
                    <div class="text-sm text-gray-900 font-semibold">{{ $d['date'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $stateLabels[$d['day_end_state']] ?? '—' }}
                        ·
                        {{ $rumLabels[$d['rumination_level']] ?? '—' }}
                    </div>

                    @if(!empty($d['takeaway']))
                        <div class="mt-2 text-sm text-gray-700 italic">
                            “{{ $d['takeaway'] }}”
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
