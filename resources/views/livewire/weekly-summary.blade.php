<div class="max-w-4xl mx-auto px-4 py-8">
    @php
        use Carbon\Carbon;

        $labels = [
            'calm' => '😌 Tranquilo',
            'good' => '🙂 Bien',
            'neutral' => '😐 Neutral',
            'tired' => '😩 Agotado',
            'frustrated' => '😤 Frustrado',
        ];
        $order = ['good', 'calm', 'neutral', 'tired', 'frustrated'];

        $stateLabels = [
            'calm' => 'En paz',
            'satisfied' => 'Satisfecho',
            'tired' => 'Cansado',
            'tense' => 'Tenso',
        ];
        $stateOrder = ['satisfied', 'calm', 'tired', 'tense'];

        $rumLabels = [
            'none' => 'Sin vueltas',
            'controlled' => 'Controlado',
            'worried' => 'Me preocupa',
        ];
        $rumOrder = ['none', 'controlled', 'worried'];

        $start = Carbon::parse($weekStart)->locale('es');
        $end = Carbon::parse($weekEnd)->locale('es');

        $humanWeekRange =
            'Del ' .
            mb_strtolower($start->translatedFormat('l j')) .
            ' al ' .
            mb_strtolower($end->translatedFormat('l j')) .
            ' de ' .
            mb_strtolower($end->translatedFormat('F'));
    @endphp

    <div class="flex items-end justify-between mb-6">
        <div>
            <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>
            <div class="text-sm text-gray-500 mt-1">Resumen semanal</div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Semana seleccionada</div>
                <div class="mt-1 text-lg font-semibold text-gray-900">
                    {{ ucfirst($humanWeekRange) }}
                </div>
                <div class="mt-1 text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($weekStart)->format('d-m-Y') }}
                    →
                    {{ \Carbon\Carbon::parse($weekEnd)->format('d-m-Y') }}
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="prevWeek"
                    class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-medium bg-white text-gray-700 hover:bg-gray-50">
                    ← Semana anterior
                </button>

                <button wire:click="nextWeek"
                    class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-medium bg-white text-gray-700 hover:bg-gray-50">
                    Semana siguiente →
                </button>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Total bloques</div>
                <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $total }}</div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Completados</div>
                <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $completed }}</div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Cumplimiento</div>
                <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $pct }}%</div>
            </div>
        </div>
    </div>

    <div class="mt-4 rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
        <div class="flex items-center justify-between gap-4 mb-3">
            <div>
                <div class="text-sm font-semibold text-gray-900">Lectura de la semana</div>
                <div class="text-xs text-gray-500 mt-1">
                    Un texto breve generado a partir de tus bloques y cierres de esta semana.
                </div>
            </div>

            <button wire:click="generateWeeklyReflection" wire:loading.attr="disabled"
                wire:target="generateWeeklyReflection"
                class="shrink-0 rounded-xl border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                <span wire:loading.remove wire:target="generateWeeklyReflection">
                    {{ $weeklyReflection ? 'Regenerar' : 'Generar' }}
                </span>
                <span wire:loading wire:target="generateWeeklyReflection">
                    Generando...
                </span>
            </button>
        </div>

        @if (!$weeklyReflection)
            <div class="rounded-xl bg-gray-50 px-4 py-4 text-sm text-gray-500">
                Aún no has generado la lectura de esta semana.
            </div>
        @else
            <div
                class="rounded-xl bg-emerald-50/40 border border-emerald-100 px-4 py-4 text-sm leading-7 text-gray-700">
                {{ $weeklyReflection }}
            </div>
        @endif
    </div>

    <div class="mt-4 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">Por tipo</div>
            <div class="text-xs text-gray-500 mt-1">Cómo se ha repartido tu semana entre los distintos bloques.</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr class="border-b border-gray-200">
                        <th class="text-left font-medium px-6 py-3">Tipo</th>
                        <th class="text-right font-medium px-6 py-3">Completados</th>
                        <th class="text-right font-medium px-6 py-3">Total</th>
                        <th class="text-right font-medium px-6 py-3">Min. completados</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700">
                    @forelse($byType as $row)
                        <tr class="border-b border-gray-100 last:border-b-0">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block h-2.5 w-2.5 rounded-full"
                                        style="background-color: {{ $row->color ?? '#10B981' }}"></span>
                                    <span class="text-gray-900 font-medium">{{ $row->type_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">{{ (int) $row->completed }}</td>
                            <td class="px-6 py-4 text-right">{{ (int) $row->total }}</td>
                            <td class="px-6 py-4 text-right">{{ (int) $row->est_minutes_completed }}</td>
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

    <div class="mt-4 rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <div class="text-sm font-semibold text-gray-900">Clima emocional</div>
                <div class="text-xs text-gray-500 mt-1">
                    Emociones registradas al completar bloques durante esta semana.
                </div>
            </div>
        </div>

        @if (($emotionTotal ?? 0) === 0)
            <div class="text-sm text-gray-500">
                Aún no hay emociones registradas esta semana.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($order as $k)
                    @php
                        $count = (int) ($emotionCounts[$k] ?? 0);
                        $emotionPct = $emotionTotal > 0 ? round(($count / $emotionTotal) * 100) : 0;
                    @endphp

                    @if ($count > 0)
                        <div class="flex items-center gap-3">
                            <div class="w-40 text-sm text-gray-700">{{ $labels[$k] }}</div>

                            <div class="flex-1">
                                <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-2.5 bg-emerald-500" style="width: {{ $emotionPct }}%"></div>
                                </div>
                            </div>

                            <div class="w-20 text-right text-sm text-gray-600">
                                {{ $emotionPct }}% ({{ $count }})
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-4 text-xs text-gray-500">
                Cuenta solo bloques completados con emoción.
            </div>
        @endif
    </div>

    <div class="mt-4 rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
        <div class="text-sm font-semibold text-gray-900 mb-1">Cierre del día</div>
        <div class="text-xs text-gray-500 mb-4">
            Cómo suele terminar tu semana y qué nivel de rumiación aparece.
        </div>

        <div class="text-xs font-semibold uppercase tracking-wide text-gray-600 mb-3">Cómo termina la semana</div>

        @if (($dayEndTotal ?? 0) === 0)
            <div class="text-sm text-gray-500">Aún no hay cierres registrados esta semana.</div>
        @else
            <div class="space-y-3">
                @foreach ($stateOrder as $k)
                    @php
                        $count = (int) ($dayEndCounts[$k] ?? 0);
                        $statePct = $dayEndTotal > 0 ? round(($count / $dayEndTotal) * 100) : 0;
                    @endphp

                    @if ($count > 0)
                        <div class="flex items-center gap-3">
                            <div class="w-32 text-sm text-gray-700">{{ $stateLabels[$k] }}</div>
                            <div class="flex-1">
                                <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-2.5 bg-emerald-500" style="width: {{ $statePct }}%"></div>
                                </div>
                            </div>
                            <div class="w-20 text-right text-sm text-gray-600">{{ $statePct }}%
                                ({{ $count }})</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="mt-6 text-xs font-semibold uppercase tracking-wide text-gray-600 mb-3">Rumiación</div>

        @if (($ruminationTotal ?? 0) === 0)
            <div class="text-sm text-gray-500">Aún no hay datos de rumiación esta semana.</div>
        @else
            <div class="space-y-3">
                @foreach ($rumOrder as $k)
                    @php
                        $count = (int) ($ruminationCounts[$k] ?? 0);
                        $rumPct = $ruminationTotal > 0 ? round(($count / $ruminationTotal) * 100) : 0;
                    @endphp

                    @if ($count > 0)
                        <div class="flex items-center gap-3">
                            <div class="w-32 text-sm text-gray-700">{{ $rumLabels[$k] }}</div>
                            <div class="flex-1">
                                <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-2.5 bg-emerald-500" style="width: {{ $rumPct }}%"></div>
                                </div>
                            </div>
                            <div class="w-20 text-right text-sm text-gray-600">{{ $rumPct }}%
                                ({{ $count }})</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-4 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="text-sm font-semibold text-gray-900">Cierres por día</div>
            <div class="text-xs text-gray-500 mt-1">Detalle diario de cómo terminó cada jornada.</div>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($closuresByDay as $d)
                @php
                    $dateHuman = \Carbon\Carbon::parse($d['date'])->locale('es')->translatedFormat('l j \d\e F');
                @endphp

                <div class="px-4 py-4">
                    <div class="text-sm text-gray-900 font-semibold">{{ ucfirst($dateHuman) }}</div>

                    <div class="text-sm text-gray-600 mt-1">
                        {{ $stateLabels[$d['day_end_state']] ?? '—' }}
                        ·
                        {{ $rumLabels[$d['rumination_level']] ?? '—' }}
                    </div>

                    @if (!empty($d['takeaway']))
                        <div class="mt-2 text-sm text-gray-700 italic leading-6">
                            “{{ $d['takeaway'] }}”
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-4 py-6 text-sm text-gray-500">
                    No hay cierres registrados esta semana.
                </div>
            @endforelse
        </div>
    </div>
</div>
