<div>
    <style>
        html {
        scroll-behavior: smooth;
        }
    </style>
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
                            Próximos pasos · producto en construcción
                        </div>
                    </div>

                    <a href="{{ auth()->check() ? route('foco.today') : route('register') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                        {{ auth()->check() ? 'Ir a FOCO' : 'Empezar con FOCO' }}
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

                <!-- Hero -->
                <div class="mt-14 grid lg:grid-cols-2 gap-10 items-start">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs text-gray-700 ring-1 ring-gray-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                            Roadmap corto y práctico
                        </div>

                        <h1 class="mt-5 text-4xl sm:text-5xl font-bold tracking-tight text-gray-900">
                            Próximos pasos de <span class="text-emerald-700">FOCO</span>
                        </h1>

                        <p class="mt-5 text-base sm:text-lg leading-relaxed text-gray-600">
                            La base (agenda diaria) ya funciona. Ahora toca añadir las capas que convierten FOCO en algo
                            realmente útil: reflexión guiada, visión semanal y un resumen más claro.
                        </p>

                        <div class="mt-10 grid sm:grid-cols-3 gap-3 text-sm text-gray-600">
                            <div class="rounded-xl bg-white/60 ring-1 ring-gray-200 p-3">
                                <div class="font-semibold text-gray-900">Reflexión</div>
                                <div class="mt-1">Preguntas guiadas.</div>
                            </div>
                            <div class="rounded-xl bg-white/60 ring-1 ring-gray-200 p-3">
                                <div class="font-semibold text-gray-900">Semana</div>
                                <div class="mt-1">Vista global.</div>
                            </div>
                            <div class="rounded-xl bg-white/60 ring-1 ring-gray-200 p-3">
                                <div class="font-semibold text-gray-900">Resumen</div>
                                <div class="mt-1">Más claro y útil.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel lateral -->
                    <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200/70 flex items-center justify-between">
                            <div class="text-sm font-semibold text-gray-900">En construcción</div>
                            <div class="text-xs text-gray-500">Prioridad: experiencia</div>
                        </div>

                        <div class="p-5 space-y-4">
                            <div class="rounded-2xl bg-emerald-50 ring-1 ring-emerald-200 p-4">
                                <div class="text-sm font-semibold text-emerald-900">Objetivo</div>
                                <div class="mt-1 text-sm text-emerald-800">
                                    Que la planificación no sea solo “hacer cosas”, sino entender qué pasa cuando no salen,
                                    y ajustar la semana con intención.
                                </div>
                            </div>

                            <div class="rounded-2xl bg-amber-50 ring-1 ring-amber-200 p-4">
                                <div class="text-sm font-semibold text-amber-900">Nota</div>
                                <div class="mt-1 text-sm text-amber-800">
                                    Las preguntas y el enfoque de reflexión se implementarán con supervisión psicológica.
                                </div>
                            </div>

                            <div class="text-xs text-gray-500">
                                Este roadmap es flexible: se prioriza lo que más valor aporta al usuario.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección pasos -->
                <div id="pasos" class="scroll-mt-32 mt-16 grid lg:grid-cols-3 gap-6">
                    <!-- Paso 1 -->
                    <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">1) Cierre de ayer (guiado)</h2>
                            <span class="text-xs rounded-full bg-gray-900/5 px-2 py-1 text-gray-600">Alta prioridad</span>
                        </div>

                        <p class="mt-3 text-gray-600 leading-relaxed">
                            Si ayer quedó algún bloque sin hacer, FOCO te preguntará el motivo y te guiará con preguntas
                            cortas para entender el “por qué”.
                        </p>

                        <ul class="mt-4 space-y-2 text-sm text-gray-600">
                            <li>• Detectar bloques pendientes de ayer.</li>
                            <li>• Motivo rápido (selección + texto opcional).</li>
                            <li>• Preguntas dinámicas según la respuesta (árbol simple).</li>
                            <li>• Resultado: aprendizaje + ajuste recomendado.</li>
                        </ul>
                    </div>

                    <!-- Paso 2 -->
                    <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">2) Vista semanal</h2>
                            <span class="text-xs rounded-full bg-gray-900/5 px-2 py-1 text-gray-600">Siguiente</span>
                        </div>

                        <p class="mt-3 text-gray-600 leading-relaxed">
                            Una vista de semana para ver tus bloques como un mapa. Rápida, clara y sin ruido.
                        </p>

                        <ul class="mt-4 space-y-2 text-sm text-gray-600">
                            <li>• Día a día con 3–5 bloques.</li>
                            <li>• Prioridad y tiempo visible de un vistazo.</li>
                            <li>• Indicadores de completado / pendiente.</li>
                            <li>• Accesos rápidos para mover o replanificar.</li>
                        </ul>
                    </div>

                    <!-- Paso 3 -->
                    <div class="rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">3) Mejorar el resumen</h2>
                            <span class="text-xs rounded-full bg-gray-900/5 px-2 py-1 text-gray-600">Mejora</span>
                        </div>

                        <p class="mt-3 text-gray-600 leading-relaxed">
                            El resumen debe contar una historia: qué hiciste, qué costó, qué sentiste y qué cambias la
                            próxima semana.
                        </p>

                        <ul class="mt-4 space-y-2 text-sm text-gray-600">
                            <li>• Resumen diario más visual y útil.</li>
                            <li>• Agrupar por prioridad/tiempo real.</li>
                            <li>• Integrar emociones por bloque.</li>
                            <li>• Cierre semanal con conclusiones.</li>
                        </ul>
                    </div>
                </div>

                <!-- CTA final -->
                <div class="mt-10 rounded-3xl bg-white/70 ring-1 ring-gray-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">¿Quieres probar la base actual?</div>
                        <div class="mt-1 text-gray-600">
                            La agenda ya funciona. Lo psicológico se irá integrando paso a paso.
                        </div>
                    </div>

                    <a href="{{ auth()->check() ? route('foco.today') : route('register') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition">
                        {{ auth()->check() ? 'Ir a FOCO' : 'Empezar con FOCO' }}
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

                <footer class="mt-14 pb-6 text-center text-xs text-gray-500">
                    © {{ date('Y') }} FOCO · Roadmap
                </footer>
            </div>
        </div>
    </x-guest-layout>
</div>
