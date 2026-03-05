<div>
    <x-guest-layout>
        <div class="relative overflow-hidden">
            <!-- Fondo suave -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute -top-24 left-1/2 -translate-x-1/2 h-72 w-[40rem] rounded-full bg-emerald-200/40 blur-3xl"></div>
                <div class="absolute -bottom-24 right-10 h-64 w-64 rounded-full bg-emerald-100 blur-3xl"></div>
            </div>

            <div class="max-w-6xl mx-auto px-6 py-14">
                <!-- Top bar -->
                <div class="flex items-center justify-between">
                    <div class="flex flex-col items-start">
                        <x-authentication-card-logo />
                        <div class="text-xs text-gray-500 mt-1">
                            Planificación semanal · acción real · reflexión
                        </div>
                    </div>

                    <a href="{{ auth()->check() ? route('foco.today') : route('register') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                        {{ auth()->check() ? 'Ir a FOCO' : 'Empezar con FOCO' }}
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

                <!-- Hero -->
                <div class="grid lg:grid-cols-2 gap-10 mt-14 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs text-gray-700 ring-1 ring-gray-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                            Beta en construcción · enfoque supervisado por psicología
                        </div>

                        <h1 class="mt-5 text-4xl sm:text-5xl font-bold tracking-tight text-gray-900">
                            Una agenda semanal diseñada para convertir tareas en <span class="text-emerald-700">acciones reales</span>.
                        </h1>

                        <p class="mt-5 text-base sm:text-lg leading-relaxed text-gray-600">
                            FOCO transforma tareas grandes en eventos pequeños, con tiempo y prioridad.
                            Y cuando terminas, registras cómo te sentiste para entender tu semana de verdad.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ auth()->check() ? route('foco.today') : route('register') }}"
                               class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                                {{ auth()->check() ? 'Abrir mi día' : 'Crear cuenta gratis' }}
                            </a>
                        </div>

                        <!-- micro bullets -->
                        <div class="mt-8 grid sm:grid-cols-3 gap-3 text-sm text-gray-600">
                            <div class="rounded-xl bg-white/60 ring-1 ring-gray-200 p-3">
                                <div class="font-semibold text-gray-900">Tiempo</div>
                                <div class="mt-1">Bloques con duración real.</div>
                            </div>
                            <div class="rounded-xl bg-white/60 ring-1 ring-gray-200 p-3">
                                <div class="font-semibold text-gray-900">Prioridad</div>
                                <div class="mt-1">Lo importante primero.</div>
                            </div>
                            <div class="rounded-xl bg-white/60 ring-1 ring-gray-200 p-3">
                                <div class="font-semibold text-gray-900">Emoción</div>
                                <div class="mt-1">Cierre para aprender.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Mock visual -->
                    <div class="lg:justify-self-end">
                        <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-200/70 flex items-center justify-between">
                                <div class="text-sm font-semibold text-gray-900">Semana · Vista rápida</div>
                                <div class="text-xs text-gray-500">3–5 bloques al día</div>
                            </div>

                            <div class="p-5 space-y-4">
                                <div class="grid grid-cols-7 gap-2 text-xs text-gray-500">
                                    <div class="text-center">L</div><div class="text-center">M</div><div class="text-center">X</div>
                                    <div class="text-center">J</div><div class="text-center">V</div><div class="text-center">S</div><div class="text-center">D</div>
                                </div>

                                <div class="grid grid-cols-7 gap-2">
                                    @foreach (range(1, 7) as $i)
                                        <div class="rounded-2xl bg-white ring-1 ring-gray-200 p-2">
                                            <div class="h-2 w-10 rounded-full bg-emerald-600/20 mb-2"></div>
                                            <div class="space-y-1.5">
                                                <div class="h-2 w-full rounded bg-gray-200/70"></div>
                                                <div class="h-2 w-4/5 rounded bg-gray-200/70"></div>
                                                <div class="h-2 w-3/5 rounded bg-gray-200/70"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="rounded-2xl bg-amber-50 ring-1 ring-amber-200 p-4">
                                    <div class="text-sm font-semibold text-amber-900">Cierre semanal</div>
                                    <div class="mt-1 text-sm text-amber-800">
                                        Qué salió bien, qué te drenó energía, y qué vas a ajustar la semana que viene.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="mt-3 text-xs text-gray-500 text-center lg:text-left">
                            Mock visual orientativo (la interfaz real sigue evolucionando).
                        </p>
                    </div>
                </div>

                <!-- Secciones -->
                <div id="como-funciona" class="mt-16 grid lg:grid-cols-3 gap-6 scroll-mt-24">
                    <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900">El problema</h2>
                        <p class="mt-3 text-gray-600 leading-relaxed">
                            Demasiadas herramientas, listas infinitas y tareas sin fin.
                            Al final del día no sabes si avanzaste en lo importante o solo estuviste ocupado.
                        </p>
                    </div>

                    <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900">La idea</h2>
                        <p class="mt-3 text-gray-600 leading-relaxed">
                            FOCO es una agenda semanal para convertir tareas grandes en eventos pequeños,
                            delimitados por tiempo y prioridad. Menos carga mental. Más ejecución.
                        </p>
                    </div>

                    <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900">Cómo funciona</h2>
                        <ul class="mt-3 text-gray-600 space-y-2 leading-relaxed">
                            <li>• Planificas en bloques manejables.</li>
                            <li>• Cada bloque tiene tiempo y prioridad.</li>
                            <li>• Al terminar, registras emoción.</li>
                            <li>• Al final, ves tu semana con claridad.</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 grid lg:grid-cols-2 gap-6">
                    <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900">En qué se diferencia</h2>
                        <p class="mt-3 text-gray-600 leading-relaxed">
                            FOCO no busca añadir más tareas, sino ayudarte a ejecutar mejor las que ya importan.
                            Combina planificación práctica con principios de psicología para entender no solo qué haces,
                            sino también cómo te afecta.
                        </p>
                    </div>

                    <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900">Estado del proyecto</h2>
                        <p class="mt-3 text-gray-600 leading-relaxed">
                            FOCO está en desarrollo: la base es una agenda simple y funcional, y se irán incorporando
                            capas de reflexión y seguimiento emocional supervisadas desde una perspectiva psicológica.
                        </p>

                        <div class="mt-5">
                            <a href="{{ auth()->check() ? route('foco.today') : route('register') }}"
                               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                                {{ auth()->check() ? 'Ir a FOCO' : 'Empezar con FOCO' }}
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CTA Próximos pasos -->
                <div class="mt-12 rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                    <div>
                        <div class="text-lg font-semibold text-gray-900">
                            ¿Quieres ver hacia dónde va FOCO?
                        </div>
                        <div class="mt-1 text-gray-600">
                            Consulta los próximos pasos del proyecto y las funcionalidades que están en desarrollo.
                        </div>
                    </div>

                    <a href="{{ route('next.steps') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                        Ver próximos pasos
                        <span aria-hidden="true">→</span>
                    </a>

                </div>

                <footer class="mt-14 pb-6 text-center text-xs text-gray-500">
                    © {{ date('Y') }} FOCO · En construcción
                </footer>
            </div>
        </div>
    </x-guest-layout>
</div>
