{{-- resources/views/livewire/today-focus.blade.php --}}

<div class="max-w-3xl mx-auto px-4 py-8"
     wire:poll.10s="pollRefresh">
    <div class="flex items-end justify-between mb-6">
        <div>

            @php
                $todayText = \Carbon\Carbon::now()->locale('es')->isoFormat('dddd D [de] MMMM');
            @endphp

            <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>


            <div class="text-sm text-gray-600 mt-1">
                @if($completed === $total && $total > 0)
                    ✅ Buen trabajo hoy, {{ ucfirst($todayText) }}.
                @else
                    👋 Hola, {{ ucfirst($todayText) }}. Este es tu plan para hoy.
                @endif
            </div>


            <div class="text-sm text-gray-500 mt-1">
                Hoy · {{ $completed }} / {{ $total }}
                @if($total > 0 && $completed === $total)
                    <span
                        class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200"
                        x-data
                        x-init="$el.animate([{transform:'scale(1)', opacity:1},{transform:'scale(1.06)', opacity:1},{transform:'scale(1)', opacity:1}], {duration: 380, easing: 'ease-out'})"
                    >
                        Completado
                    </span>
                @endif
            </div>
        </div>

        <button
            type="button"
            wire:click="reapplyTodayTemplate"
            class="text-gray-600 hover:text-gray-900 underline underline-offset-4"
        >
            Aplicar mi semana tipo a hoy
        </button>
    </div>

   @if(!$total)
    @php
        $todayText = \Carbon\Carbon::now()->locale('es')->isoFormat('dddd D [de] MMMM');
    @endphp

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6">
        <div class="flex items-start gap-3">
            <div class="text-2xl leading-none">👋</div>
            <div class="min-w-0">
                <div class="font-semibold text-gray-900">
                    Bienvenido, {{ ucfirst($todayText) }}
                </div>

                <div class="mt-1 text-sm text-gray-600">
                    Hoy aún no tienes bloques asignados.
                    Para empezar, configura tu día (se hace desde <span class="font-medium">Semana tipo</span>) y luego vuelve aquí para ir completándolos.
                </div>
            </div>
        </div>

        <div class="mt-4 rounded-xl bg-gray-50 border border-gray-200 p-4">
            <div class="text-sm font-semibold text-gray-900">Cómo empezar (30 segundos)</div>
            <ol class="mt-2 space-y-1 text-sm text-gray-700 list-decimal list-inside">
                <li>Pulsa <span class="font-medium">Configurar hoy</span> y asigna algunos bloques al día de hoy.</li>
                <li>Vuelve a esta pantalla y márcalos como completados cuando los hagas.</li>
            </ol>
            <div class="mt-2 text-xs text-gray-500">
                Consejo: con 3–6 bloques es perfecto para empezar.
            </div>
        </div>

        <div class="mt-5 flex flex-col sm:flex-row flex-wrap gap-3">
            <a href="{{ route('foco.week', ['day' => now()->dayOfWeekIso]) }}"
               class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                Configurar hoy
            </a>

            <a href="{{ route('foco.library') }}"
               class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold bg-white text-gray-900 border border-gray-200 hover:bg-gray-50">
                Ver biblioteca
            </a>

            {{-- <a href="{{ route('foco.tutorial') }}" --}}
            <a
               class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
                Ver tutorial
            </a>
        </div>

        <div class="mt-4 text-xs text-gray-500">
            Si vas con prisa: asigna 1 bloque importante + 1 ligero + 1 autocuidado. Y listo.
        </div>
    </div>
@else

        @php
            /**
             * Segmentación visual (solo para poner “separadores” de tramo)
             * - Morning: 06:00-11:59
             * - Midday: 12:00-15:59
             * - Afternoon: 16:00-20:59
             * - Night: 21:00-05:59
             */
            $segmentOf = function (?string $hhmm) {
                if (!$hhmm) return 'Sin hora';

                $parts = explode(':', $hhmm);
                $h = (int)($parts[0] ?? 0);
                $m = (int)($parts[1] ?? 0);
                $mins = $h * 60 + $m;

                if ($mins >= 360 && $mins < 720) return 'Mañana';
                if ($mins >= 720 && $mins < 960) return 'Mediodía';
                if ($mins >= 960 && $mins < 1260) return 'Tarde';
                return 'Noche';
            };

            $lastSegment = null;
        @endphp

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">

            {{-- Timeline wrapper --}}
            <div class="p-4 sm:p-6">

                {{-- Línea vertical (fondo) --}}
                <div class="relative">
                    {{-- linea --}}
                    <div class="absolute left-[76px] sm:left-[88px] top-0 bottom-0 w-px bg-gray-200"></div>

                    <div class="space-y-3">

                        @foreach($blocks as $b)

                            @php
                                $p = $b['priority'] ?? 'no_importante';

                                $priorityBadge = match ($p) {
                                    'urgente' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
                                    'importante' => 'bg-amber-50 text-amber-800 ring-1 ring-amber-200',
                                    default => 'bg-gray-50 text-gray-600 ring-1 ring-gray-200',
                                };

                                $priorityLabel = match ($p) {
                                    'urgente' => 'Urgente',
                                    'importante' => 'Importante',
                                    default => 'No importante',
                                };
                            @endphp

                            @php
                                $seg = $segmentOf($b['start_time'] ?? null);
                                $showSegment = ($seg !== $lastSegment);
                                $lastSegment = $seg;

                                $hasTime = !empty($b['start_time']);
                                $timeLabel = $hasTime ? $b['start_time'] : '—';
                            @endphp

                            {{-- Separador de tramo --}}
                            @if($showSegment)
                                <div class="pt-2">
                                    <div class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-gray-50 text-gray-700 ring-1 ring-gray-200">
                                        {{ $seg }}
                                    </div>
                                </div>
                            @endif

                            <div wire:key="today-block-{{ $b['id'] }}"
                                 class="relative flex gap-4">

                                {{-- Columna hora --}}
                                <div class="w-[64px] sm:w-[76px] shrink-0 text-right">
                                    <div class="text-xs font-semibold text-gray-700">
                                        {{ $timeLabel }}
                                    </div>
                                    @if(!is_null($b['estimated_minutes']))
                                        <div class="mt-0.5 text-[11px] text-gray-400">
                                            {{ $b['estimated_minutes'] }}m
                                        </div>
                                    @endif
                                </div>

                                {{-- Punto en la línea --}}
                                <div class="relative w-[24px] shrink-0">

                                </div>

                                {{-- Tarjeta bloque --}}
                                <div class="flex-1 min-w-0">
                                    <div class="rounded-2xl border transition p-4
                                        {{ $b['completed']
                                            ? 'border-emerald-200 bg-gradient-to-l from-emerald-100/70 via-emerald-50/60 to-white'
                                            : 'border-gray-200 bg-white hover:bg-gray-50'
                                        }}">

                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 sm:gap-4">

    {{-- FILA 1 (móvil) / COLUMNA IZQ (sm): Badge --}}
    <div class="flex items-center justify-between sm:justify-start">
        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $priorityBadge }}">
            {{ $priorityLabel }}
        </span>

        {{-- Acciones en móvil (a la derecha) --}}
        <div class="flex items-center gap-1 shrink-0 sm:hidden">
            @if(!empty($b['notes']))
                <button type="button"
                        x-on:click="$dispatch('foco-open-notes', { title: @js($b['title']), type: @js($b['type_name']), notes: @js($b['notes']) })"
                        class="rounded-lg p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50"
                        aria-label="Ver detalles" title="Ver detalles del bloque">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                        <path d="M10 3c-4.5 0-8 3.5-9 7 1 3.5 4.5 7 9 7s8-3.5 9-7c-1-3.5-4.5-7-9-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-6.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z"/>
                    </svg>
                </button>
            @endif

            <button type="button"
                    wire:click.stop="deleteBlock({{ $b['id'] }})"
                    wire:confirm="¿Eliminar este bloque?"
                    class="rounded-lg p-2 text-gray-400 hover:text-red-600 hover:bg-red-50"
                    aria-label="Eliminar bloque" title="Eliminar bloque para este día">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM7.22 7.22a.75.75 0 0 1 1.06 0L10 8.94l1.72-1.72a.75.75 0 1 1 1.06 1.06L11.06 10l1.72 1.72a.75.75 0 1 1-1.06 1.06L10 11.06l-1.72 1.72a.75.75 0 1 1-1.06-1.06L8.94 10 7.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>

    {{-- COLUMNA CENTRAL (sm): Título + meta (clicable). En móvil ocupa toda la línea --}}
    <button type="button"
            wire:click="toggleCompleted({{ $b['id'] }})"
            class="text-left w-full sm:flex-1 sm:min-w-0">
        <div class="flex items-center gap-2">
            <span class="inline-block h-2.5 w-2.5 rounded-full shrink-0"
                  style="background-color: {{ $b['color'] }}"></span>

            <div class="text-gray-900 font-semibold leading-snug break-words sm:truncate">
                <span class="{{ $b['completed'] ? 'line-through text-gray-400' : '' }}">
                    {{ $b['title'] }}
                </span>
            </div>
        </div>

        <div class="text-xs text-gray-500 mt-1">
            {{ $b['type_name'] }}
            @if(!is_null($b['estimated_minutes']))
                · {{ $b['estimated_minutes'] }} min
            @endif
        </div>
    </button>

    {{-- COLUMNA DERECHA (sm): Acciones a la derecha --}}
    <div class="hidden sm:flex items-center gap-1 shrink-0">
        @if(!empty($b['notes']))
            <button type="button"
                    x-on:click="$dispatch('foco-open-notes', { title: @js($b['title']), type: @js($b['type_name']), notes: @js($b['notes']) })"
                    class="rounded-lg p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50"
                    aria-label="Ver detalles" title="Ver detalles del bloque">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                    <path d="M10 3c-4.5 0-8 3.5-9 7 1 3.5 4.5 7 9 7s8-3.5 9-7c-1-3.5-4.5-7-9-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-6.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z"/>
                </svg>
            </button>
        @endif

        <button type="button"
                wire:click.stop="deleteBlock({{ $b['id'] }})"
                wire:confirm="¿Eliminar este bloque?"
                class="rounded-lg p-2 text-gray-400 hover:text-red-600 hover:bg-red-50"
                aria-label="Eliminar bloque" title="Eliminar bloque para este día">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM7.22 7.22a.75.75 0 0 1 1.06 0L10 8.94l1.72-1.72a.75.75 0 1 1 1.06 1.06L11.06 10l1.72 1.72a.75.75 0 1 1-1.06 1.06L10 11.06l-1.72 1.72a.75.75 0 1 1-1.06-1.06L8.94 10 7.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

</div>


                                        {{-- Emojis (solo si completado) --}}
                                        @if($b['completed'])
    <div class="mt-3 flex flex-wrap gap-2 text-lg">
        @php
            $emotions = [
                'calm' => '😌',
                'good' => '🙂',
                'neutral' => '😐',
                'tired' => '😩',
                'frustrated' => '😤',
            ];
        @endphp

        @foreach($emotions as $key => $emoji)
            <button
                type="button"
                wire:click="setEmotion({{ $b['id'] }}, '{{ $key }}')"
                class="transition {{ ($b['emotion'] ?? null) === $key ? 'scale-125' : 'opacity-50 hover:opacity-100' }}"
            >
                {{ $emoji }}
            </button>
        @endforeach
    </div>
@endif

                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- Cierre del día --}}
    @if($this->isDayComplete || $dayEndState || $ruminationLevel || trim($takeaway))
        <div class="mt-6 rounded-2xl border border-gray-200 bg-white shadow-sm p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-gray-900">🌙 Cierre del día</div>
                    <div class="text-xs text-gray-500 mt-1">Editable hasta que termine el día.</div>
                </div>

                @if($this->canEditClosure)
                    <button type="button"
                            wire:click="openClosingSurvey"
                            class="text-sm text-gray-600 hover:text-gray-900 underline underline-offset-4">
                        {{ $dayEndState || $ruminationLevel || trim($takeaway) ? 'Editar' : 'Hacer cierre' }}
                    </button>
                @endif
            </div>

            @if($dayEndState || $ruminationLevel || trim($takeaway))
                @php
                    $stateLabels = [
                        'calm' => 'En paz',
                        'satisfied' => 'Satisfecho',
                        'tired' => 'Cansado',
                        'tense' => 'Tenso',
                    ];
                    $rumLabels = [
                        'none' => 'Sin vueltas',
                        'controlled' => 'Controlado',
                        'worried' => 'Me preocupa',
                    ];
                @endphp

                <div class="mt-4 space-y-2 text-sm text-gray-700">
                    <div>
                        <span class="font-semibold text-gray-900">Estado:</span>
                        {{ $stateLabels[$dayEndState] ?? '—' }}
                    </div>

                    <div>
                        <span class="font-semibold text-gray-900">Cabeza:</span>
                        {{ $rumLabels[$ruminationLevel] ?? '—' }}
                    </div>

                    @if(trim($takeaway))
                        <div class="pt-2 text-gray-700 italic">
                            “{{ $takeaway }}”
                        </div>
                    @endif
                </div>
            @else
                <div class="mt-4 text-sm text-gray-600">
                    No has cerrado el día todavía.
                </div>
            @endif

            @if(!$this->isDayComplete)
                <div class="mt-2 text-xs text-gray-500 text-end">
                    Nota: aún tienes bloques sin completar hoy.
                </div>
            @endif
        </div>
    @endif


    {{-- Overlays --}}
    @if($showCompletedOverlay)
        <div class="fixed inset-0 z-40 bg-white/90 backdrop-blur-sm flex items-center justify-center px-6">
            <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white shadow-sm p-6 text-center">
                <div class="text-3xl">🎉</div>
                <div class="mt-3 text-lg font-semibold text-gray-900">Día completado</div>
                <div class="mt-2 text-sm text-gray-600">Has terminado tus bloques de hoy.</div>

                <div class="mt-6 flex flex-col gap-3">
                    <button type="button"
                            wire:click="openClosingSurvey"
                            class="w-full rounded-xl px-4 py-2 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                        Cerrar el día
                    </button>

                    <button type="button"
                            wire:click="closeOverlays"
                            class="w-full rounded-xl px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showClosingSurvey)
        <div class="fixed inset-0 z-50 bg-black/30 flex items-center justify-center px-6">
            <form wire:submit.prevent="saveDayClosure"
                  class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white shadow-sm p-6">

                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">🌙 Cierre del día</div>
                        <div class="text-sm text-gray-500 mt-1">Editable hasta que termine el día.</div>
                    </div>
                    <button type="button" wire:click="closeOverlays" class="text-gray-400 hover:text-gray-700">✕</button>
                </div>

                <div class="mt-5 space-y-5">
                    <div>
                        <div class="text-sm font-semibold text-gray-900 mb-2">¿Cómo terminas el día?</div>
                        <div class="flex flex-wrap gap-2">
                            @php $opts = ['calm'=>'En paz','satisfied'=>'Satisfecho','tired'=>'Cansado','tense'=>'Tenso']; @endphp
                            @foreach($opts as $k => $label)
                                <button type="button"
                                        wire:click="$set('dayEndState','{{ $k }}')"
                                        class="rounded-xl px-3 py-2 text-sm border {{ $dayEndState===$k ? 'border-emerald-300 bg-emerald-50 text-emerald-800' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-gray-900 mb-2">¿Te queda algo rondando en la cabeza?</div>
                        <div class="flex flex-wrap gap-2">
                            @php $opts2 = ['none'=>'No, cierro tranquilo','controlled'=>'Sí, controlado','worried'=>'Sí, me preocupa']; @endphp
                            @foreach($opts2 as $k => $label)
                                <button type="button"
                                        wire:click="$set('ruminationLevel','{{ $k }}')"
                                        class="rounded-xl px-3 py-2 text-sm border {{ $ruminationLevel===$k ? 'border-emerald-300 bg-emerald-50 text-emerald-800' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Hoy me llevo… (opcional)</label>
                        <input type="text"
                               wire:model.defer="takeaway"
                               maxlength="180"
                               class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                               placeholder="Una frase corta" />
                        <div class="mt-1 text-xs text-gray-500">Máx. 180 caracteres.</div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                            wire:click="closeOverlays"
                            class="rounded-xl px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="rounded-xl px-4 py-2 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                        Guardar cierre
                    </button>
                </div>
            </form>
        </div>
    @endif


    {{-- Toast --}}
    <div
        x-data="{ show:false, message:'' }"
        x-on:toast.window="
            message = $event.detail.message || '';
            show = true;
            clearTimeout(window.__focoToastTimer);
            window.__focoToastTimer = setTimeout(() => show = false, 1800);
        "
        x-show="show"
        x-transition.opacity
        class="fixed bottom-6 right-6 z-50 rounded-xl bg-white border border-gray-200 shadow-sm px-4 py-2 text-sm text-gray-900"
        style="display:none;"
    >
        <span x-text="message"></span>
    </div>

    {{-- Drawer lateral: Detalles del bloque --}}
<div
    x-data="{
        open: false,
        title: '',
        type: '',
        notes: '',
        openWith(payload) {
            this.title = payload.title || '';
            this.type  = payload.type || '';
            this.notes = payload.notes || '';
            this.open  = true;
        }
    }"
    x-on:foco-open-notes.window="openWith($event.detail)"
    x-on:keydown.escape.window="open = false"
>
    {{-- Overlay --}}
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-black/25"
        @click="open = false"
        style="display:none;"
    ></div>

    {{-- Panel derecho --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform translate-x-full"
        x-transition:enter-end="transform translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform translate-x-0"
        x-transition:leave-end="transform translate-x-full"
        class="fixed right-0 top-0 z-50 h-full w-full sm:w-[420px] bg-white border-l border-gray-200 shadow-xl"
        style="display:none;"
        @click.stop
    >
            <div class="h-full flex flex-col">
                <div class="px-5 py-4 border-b border-gray-200 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm text-gray-500" x-text="type"></div>
                        <div class="text-lg font-semibold text-gray-900 truncate" x-text="title"></div>
                    </div>
                    <button type="button"
                            class="rounded-lg p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100"
                            @click="open=false"
                            aria-label="Cerrar">
                        ✕
                    </button>
                </div>

                <div class="flex-1 overflow-auto px-5 py-4">
                    <div class="text-sm text-gray-700 whitespace-pre-line leading-relaxed" x-text="notes"></div>
                </div>

                <div class="px-5 py-4 border-t border-gray-200 flex justify-end">
                    <button type="button"
                            class="rounded-xl px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-900 hover:bg-gray-200"
                            @click="open=false">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
