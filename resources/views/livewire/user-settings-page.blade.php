<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-end justify-between mb-6">
        <div>
            <div class="text-2xl font-semibold tracking-wide text-gray-900">FOCO</div>
            <div class="text-sm text-gray-500 mt-1">Ajustes</div>
        </div>

        {{-- <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('foco.today') }}" class="text-gray-600 hover:text-gray-900 underline underline-offset-4">Hoy</a>
            <a href="{{ route('foco.summary') }}" class="text-gray-600 hover:text-gray-900 underline underline-offset-4">Resumen</a>
            <a href="{{ route('foco.templates') }}" class="text-gray-600 hover:text-gray-900 underline underline-offset-4">Plantillas</a>
        </div> --}}
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6">
        <div class="grid grid-cols-1 gap-5">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Máximo de bloques diarios</label>
                <input type="number" min="1" max="20"
                       wire:model.defer="maxDailyBlocks"
                       class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox"
                       wire:model.defer="strictMode"
                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-400" />
                <div>
                    <div class="text-sm text-gray-900 font-semibold">Modo estricto</div>
                    <div class="text-xs text-gray-500">Si está activo, no podrás superar el límite diario.</div>
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Plantilla predeterminada</label>
                <select wire:model.defer="defaultTemplateId"
                        class="w-full rounded-xl bg-white border border-gray-300 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                    <option value="">— Ninguna —</option>
                    @foreach($templates as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}{{ $t->is_default ? ' (default)' : '' }}</option>
                    @endforeach
                </select>
                <div class="text-xs text-gray-500 mt-1">
                    Si eliges una, FOCO rellenará automáticamente “Hoy” cuando no haya bloques creados.
                </div>
            </div>

            <div class="flex justify-end">
                <button wire:click="save"
                        class="rounded-xl px-4 py-2 text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                    Guardar
                </button>
            </div>
        </div>
    </div>

    <div x-data="{ show:false, message:'' }"
         x-on:toast.window="message=$event.detail.message; show=true; setTimeout(()=>show=false, 1800)"
         x-show="show"
         x-transition.opacity
         class="fixed bottom-6 right-6 rounded-xl bg-white border border-gray-200 shadow-sm px-4 py-2 text-sm text-gray-900"
         style="display:none;">
        <span x-text="message"></span>
    </div>
</div>
