<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Próximo partido</x-slot>

        @if ($partido)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                {{-- Local --}}
                <div class="flex flex-col items-center gap-2">
                    @if ($partido->local?->escudo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($partido->local->escudo) }}"
                             alt="{{ $partido->local->nombre }}"
                             class="w-20 h-20 object-contain">
                    @else
                        <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-3xl font-bold">
                            {{ mb_substr($partido->local_nombre ?? '?', 0, 1) }}
                        </div>
                    @endif
                    <div class="font-bold text-center">
                        {{ $partido->local?->nombre ?? $partido->local_nombre }}
                    </div>
                    <div class="text-xs text-gray-500">Local</div>
                </div>

                {{-- Center info --}}
                <div class="flex flex-col items-center gap-2 text-center">
                    <div class="text-3xl font-bold text-gray-500">VS</div>
                    <div class="text-sm text-gray-700 dark:text-gray-300 font-semibold">
                        {{ $partido->fecha->format('d/m/Y H:i') }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $partido->competicion }} · J{{ $partido->jornada }}
                    </div>
                    @if ($partido->estadio)
                        <div class="text-xs text-gray-500">
                            <x-heroicon-o-map-pin class="w-3 h-3 inline" />
                            {{ $partido->estadio }}
                        </div>
                    @endif
                    <div class="mt-2 px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-100 rounded-full text-xs font-semibold"
                         x-data="{
                            countdown: '',
                            init() {
                                const target = new Date('{{ $countdownIso }}').getTime();
                                const update = () => {
                                    const diff = target - Date.now();
                                    if (diff <= 0) { this.countdown = '¡Empieza ya!'; return; }
                                    const d = Math.floor(diff / 86400000);
                                    const h = Math.floor((diff % 86400000) / 3600000);
                                    const m = Math.floor((diff % 3600000) / 60000);
                                    this.countdown = `${d}d ${h}h ${m}m`;
                                };
                                update();
                                setInterval(update, 30000);
                            }
                         }"
                         x-text="countdown">
                    </div>
                </div>

                {{-- Visitante --}}
                <div class="flex flex-col items-center gap-2">
                    @if ($partido->visitante?->escudo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($partido->visitante->escudo) }}"
                             alt="{{ $partido->visitante->nombre }}"
                             class="w-20 h-20 object-contain">
                    @else
                        <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-3xl font-bold">
                            {{ mb_substr($partido->visitante_nombre ?? '?', 0, 1) }}
                        </div>
                    @endif
                    <div class="font-bold text-center">
                        {{ $partido->visitante?->nombre ?? $partido->visitante_nombre }}
                    </div>
                    <div class="text-xs text-gray-500">Visitante</div>
                </div>
            </div>
        @else
            <div class="text-center text-gray-500 py-8">
                No hay partidos programados próximamente.
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
