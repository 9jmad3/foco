<div class="min-h-[calc(100vh-4rem)] bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- HERO --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-10">
            <div class="flex flex-col gap-6">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-gray-900">
                        Cómo usar <span class="text-emerald-600">FOCO</span>
                    </h1>
                    <p class="mt-3 text-gray-600 leading-relaxed">
                        FOCO te ayuda a organizar tu semana, ejecutar el día sin líos, y cerrar con una mini reflexión.
                        Menos ruido mental, más claridad.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('foco.library') }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-900 font-medium hover:bg-gray-50">
                        1) Ir a Biblioteca de bloques
                    </a>

                    <a href="{{ route('foco.week') }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-emerald-600 text-white font-medium hover:bg-emerald-700">
                        2) Planificar semana
                    </a>

                    <a href="{{ route('foco.today') }}"
                       class="inline-flex items-center justify-center px-4 py-2 rounded-xl border border-gray-300 bg-white text-gray-900 font-medium hover:bg-gray-50">
                        3) Ir a Foco diario (Hoy)
                    </a>
                </div>

                <div class="grid sm:grid-cols-4 gap-4">
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                        <div class="text-sm font-semibold text-emerald-800">1) Biblioteca</div>
                        <div class="mt-1 text-sm text-emerald-900/80">Crea tus bloques reutilizables.</div>
                    </div>
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                        <div class="text-sm font-semibold text-emerald-800">2) Planifica</div>
                        <div class="mt-1 text-sm text-emerald-900/80">Arrastra a días y pon hora/prioridad.</div>
                    </div>
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                        <div class="text-sm font-semibold text-emerald-800">3) Ejecuta</div>
                        <div class="mt-1 text-sm text-emerald-900/80">Haz los bloques del día, uno a uno.</div>
                    </div>
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                        <div class="text-sm font-semibold text-emerald-800">4) Cierra</div>
                        <div class="mt-1 text-sm text-emerald-900/80">Preguntas cortas + emociones.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PASOS --}}
        <div class="mt-8 grid lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900">Paso a paso (tu flujo real)</h2>

                <ol class="mt-4 space-y-4">
                    <li class="flex gap-3">
                        <div class="mt-0.5 h-7 w-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-semibold">1</div>
                        <div>
                            <div class="font-medium text-gray-900">Crea tus bloques en la Biblioteca</div>
                            <div class="text-sm text-gray-600">
                                Primero crea lo que vayas a usar: “Gym”, “Trabajo profundo”, “Recados”, “Estudio”, “Descanso”, etc.
                                Cuanto mejor tengas tu biblioteca, más rápido planificas después.
                            </div>
                        </div>
                    </li>

                    <li class="flex gap-3">
                        <div class="mt-0.5 h-7 w-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-semibold">2</div>
                        <div>
                            <div class="font-medium text-gray-900">Planifica tu semana (día a día)</div>
                            <div class="text-sm text-gray-600">
                                Ve a <span class="font-medium">Planificación semanal</span> y asigna bloques a cada día.
                                Si quieres, ponles <span class="font-medium">hora</span> y <span class="font-medium">prioridad</span>.
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                Prioridad disponible:
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 text-xs font-semibold">Sin prioridad</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg border border-amber-200 bg-amber-50 text-amber-800 text-xs font-semibold">Urgente</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg border border-red-200 bg-red-50 text-red-800 text-xs font-semibold">Muy prioritario</span>
                            </div>
                        </div>
                    </li>

                    <li class="flex gap-3">
                        <div class="mt-0.5 h-7 w-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-semibold">3</div>
                        <div>
                            <div class="font-medium text-gray-900">Ejecuta en el Foco diario</div>
                            <div class="text-sm text-gray-600">
                                En <span class="font-medium">Hoy</span> verás los bloques planificados para ese día.
                                Los haces uno a uno y los marcas como completados. Sin darle vueltas.
                            </div>
                        </div>
                    </li>

                    <li class="flex gap-3">
                        <div class="mt-0.5 h-7 w-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-semibold">4</div>
                        <div>
                            <div class="font-medium text-gray-900">Cierre del día (emociones + preguntas cortas)</div>
                            <div class="text-sm text-gray-600">
                                Cuando termines, haces un cierre rápido: cómo te sentiste, qué te llevas del día y qué ajustarías mañana.
                                Son preguntas pequeñas, pero te colocan la cabeza.
                            </div>
                        </div>
                    </li>

                    <li class="flex gap-3">
                        <div class="mt-0.5 h-7 w-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-sm font-semibold">5</div>
                        <div>
                            <div class="font-medium text-gray-900">Revisa el Resumen semanal</div>
                            <div class="text-sm text-gray-600">
                                En <span class="font-medium">Resumen</span> puedes ver por semanas cómo fue tu semana:
                                progreso, tendencia de emociones y sensaciones generales. Ideal para detectar patrones.
                            </div>
                        </div>
                    </li>
                </ol>
            </div>

            {{-- EJEMPLO --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900">Ejemplo real (planificación + ejecución)</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Así se ve una planificación sencilla y efectiva:
                </p>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-xl border border-gray-200 p-4">
                        <div>
                            <div class="font-medium text-gray-900">Lunes · 09:00 · Trabajo profundo</div>
                            <div class="text-sm text-gray-600">90 min · Sin distracciones</div>
                        </div>
                        <span class="text-xs font-semibold text-red-800 bg-red-50 border border-red-200 px-2 py-1 rounded-lg">Muy prioritario</span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl border border-gray-200 p-4">
                        <div>
                            <div class="font-medium text-gray-900">Lunes · 11:00 · Tareas cortas</div>
                            <div class="text-sm text-gray-600">30–45 min · Admin / recados</div>
                        </div>
                        <span class="text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-1 rounded-lg">Urgente</span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl border border-gray-200 p-4">
                        <div>
                            <div class="font-medium text-gray-900">Lunes · 19:00 · Gym</div>
                            <div class="text-sm text-gray-600">60 min · Fuerza</div>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 bg-gray-50 border border-gray-200 px-2 py-1 rounded-lg">Sin prioridad</span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl border border-gray-200 p-4">
                        <div>
                            <div class="font-medium text-gray-900">Cierre · 2–5 min</div>
                            <div class="text-sm text-gray-600">Emociones + 3 preguntas</div>
                        </div>
                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-lg">Cierre</span>
                    </div>
                </div>

                <div class="mt-5 text-sm text-gray-600">
                    <span class="font-medium text-gray-900">Clave:</span> planifica pocos bloques y cúmplelos.
                    FOCO funciona cuando lo mantienes simple.
                </div>
            </div>
        </div>

        {{-- TIPS --}}
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900">Tips que te ahorran frustración</h2>

            <div class="mt-4 grid md:grid-cols-3 gap-4">
                <div class="rounded-xl bg-gray-50 border border-gray-200 p-4">
                    <div class="font-medium text-gray-900">Bloques realistas</div>
                    <div class="mt-1 text-sm text-gray-600">
                        Mejor 3 buenos que 9 mediocres. FOCO no es “hacer más”, es “hacer lo importante”.
                    </div>
                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-200 p-4">
                    <div class="font-medium text-gray-900">Un bloque a la vez</div>
                    <div class="mt-1 text-sm text-gray-600">
                        No pienses en todo. Mira el siguiente bloque del día y ejecútalo.
                    </div>
                </div>

                <div class="rounded-xl bg-gray-50 border border-gray-200 p-4">
                    <div class="font-medium text-gray-900">Cierre corto, pero constante</div>
                    <div class="mt-1 text-sm text-gray-600">
                        Las preguntas son pequeñas, pero si las haces cada día, el resumen semanal empieza a tener sentido.
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA FINAL --}}
        <div class="mt-8 bg-emerald-600 rounded-2xl p-6 sm:p-8 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="text-xl font-semibold">Empieza bien: primero crea tu biblioteca.</div>
                    <div class="text-white/90 text-sm mt-1">
                        Crea tus bloques, planifica la semana y mañana solo tendrás que ejecutar.
                    </div>
                </div>
                <a href="{{ route('foco.library') }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-white text-emerald-700 font-semibold hover:bg-emerald-50">
                    Ir a Biblioteca
                </a>
            </div>
        </div>

    </div>
</div>
