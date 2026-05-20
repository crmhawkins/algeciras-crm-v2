<x-filament-panels::page>
    @php
        $productos = $this->getProductos();
        $categorias = $this->getCategorias();
        $metodosPago = $this->getMetodosPago();
        $clientes = $this->getClientes();
    @endphp

    <div class="grid grid-cols-12 gap-4 h-[calc(100vh-12rem)] min-h-[600px]">
        {{-- LEFT: Productos --}}
        <div class="col-span-12 lg:col-span-8 flex flex-col gap-4 overflow-hidden">
            {{-- Search --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                <input
                    type="text"
                    wire:model.live.debounce.250ms="search"
                    placeholder="Buscar producto por nombre o SKU..."
                    autofocus
                    class="w-full text-xl px-4 py-3 border-2 border-primary-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-900 dark:text-white"
                />
            </div>

            {{-- Categorías pills --}}
            <div class="flex flex-wrap gap-2 px-1">
                <button
                    wire:click="$set('categoriaFilter', null)"
                    class="px-4 py-2 rounded-full text-sm font-semibold transition
                        {{ is_null($categoriaFilter) ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    Todas
                </button>
                @foreach ($categorias as $cat)
                    <button
                        wire:click="$set('categoriaFilter', {{ $cat->id }})"
                        class="px-4 py-2 rounded-full text-sm font-semibold transition
                            {{ $categoriaFilter === $cat->id ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        {{ $cat->nombre }}
                    </button>
                @endforeach
            </div>

            {{-- Grid de productos --}}
            <div class="flex-1 overflow-y-auto bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                @if ($productos->isEmpty())
                    <div class="text-center text-gray-500 py-12">
                        No hay productos que coincidan con la búsqueda.
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach ($productos as $producto)
                            <button
                                wire:click="addToCart({{ $producto->id }})"
                                class="bg-gray-50 dark:bg-gray-900 hover:bg-primary-50 dark:hover:bg-primary-950 active:scale-95
                                       border border-gray-200 dark:border-gray-700 rounded-lg p-3 text-left transition
                                       flex flex-col gap-1">
                                <div class="aspect-square bg-gray-100 dark:bg-gray-800 rounded mb-1 flex items-center justify-center overflow-hidden">
                                    @if ($producto->imagenPrincipal)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($producto->imagenPrincipal->ruta) }}"
                                             alt="{{ $producto->nombre }}"
                                             class="w-full h-full object-cover" />
                                    @else
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="font-semibold text-sm line-clamp-2 text-gray-900 dark:text-white">
                                    {{ $producto->nombre }}
                                </div>
                                <div class="text-xs text-gray-500 font-mono">{{ $producto->sku }}</div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-lg font-bold text-primary-600">
                                        € {{ number_format((float)($producto->precio_oferta ?? $producto->precio_base), 2, ',', '.') }}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded">
                                        {{ $producto->stock }}
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Carrito --}}
        <div class="col-span-12 lg:col-span-4 flex flex-col gap-4 overflow-hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-col h-full">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center justify-between">
                    Carrito
                    @if (! empty($cart))
                        <button wire:click="clearCart"
                                class="text-xs px-2 py-1 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded">
                            Vaciar
                        </button>
                    @endif
                </h2>

                {{-- Items --}}
                <div class="flex-1 overflow-y-auto -mx-2 px-2 mb-3">
                    @if (empty($cart))
                        <div class="text-center text-gray-400 py-12 text-sm">
                            Toca un producto para empezar
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($cart as $index => $item)
                                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-900 p-2 rounded">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-sm text-gray-900 dark:text-white truncate">
                                            {{ $item['nombre'] }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            € {{ number_format($item['precio'], 2, ',', '.') }} × {{ $item['qty'] }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button wire:click="decrement({{ $index }})"
                                                class="w-7 h-7 rounded bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 font-bold">−</button>
                                        <span class="w-8 text-center font-semibold">{{ $item['qty'] }}</span>
                                        <button wire:click="increment({{ $index }})"
                                                class="w-7 h-7 rounded bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 font-bold">+</button>
                                    </div>
                                    <div class="w-20 text-right font-bold text-sm">
                                        € {{ number_format($item['precio'] * $item['qty'], 2, ',', '.') }}
                                    </div>
                                    <button wire:click="removeFromCart({{ $index }})"
                                            class="text-red-500 hover:text-red-700 p-1">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Totales --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-1">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="font-semibold">€ {{ number_format($this->getSubtotal(), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">IVA:</span>
                        <span class="font-semibold">€ {{ number_format($this->getIva(), 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-2xl font-bold pt-2 border-t border-gray-200 dark:border-gray-700 mt-2">
                        <span>TOTAL:</span>
                        <span class="text-primary-600">€ {{ number_format($this->getTotal(), 2, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Cliente --}}
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <label class="flex items-center gap-2 mb-2">
                        <input type="checkbox" wire:model.live="clienteAnonimo" class="rounded">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Cliente anónimo</span>
                    </label>
                    @if (! $clienteAnonimo)
                        <select wire:model.live="clienteId"
                                class="w-full px-3 py-2 border rounded text-sm dark:bg-gray-900 dark:text-white">
                            <option value="">Selecciona un cliente...</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- Método pago --}}
                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Método de pago:</div>
                    <div class="grid grid-cols-3 gap-2">
                        @php
                            $defaultMetodos = collect([
                                (object)['codigo' => 'efectivo', 'nombre' => 'Efectivo'],
                                (object)['codigo' => 'datafono', 'nombre' => 'Datáfono'],
                                (object)['codigo' => 'bizum', 'nombre' => 'Bizum'],
                            ]);
                            $opcionesMetodos = $metodosPago->isNotEmpty() ? $metodosPago : $defaultMetodos;
                        @endphp
                        @foreach ($opcionesMetodos as $mp)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="metodoPago" value="{{ $mp->codigo }}" class="sr-only peer">
                                <div class="px-3 py-2 rounded border-2 text-center text-sm font-semibold
                                            border-gray-200 dark:border-gray-700
                                            peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:text-primary-700
                                            dark:peer-checked:bg-primary-950 dark:peer-checked:text-primary-300">
                                    {{ $mp->nombre }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Cobrar --}}
                <button wire:click="cobrar"
                        wire:loading.attr="disabled"
                        @disabled(empty($cart))
                        class="mt-4 w-full bg-primary-600 hover:bg-primary-700 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed
                               text-white text-2xl font-bold py-4 rounded-xl shadow-lg transition">
                    <span wire:loading.remove wire:target="cobrar">COBRAR · € {{ number_format($this->getTotal(), 2, ',', '.') }}</span>
                    <span wire:loading wire:target="cobrar">Procesando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Ticket modal --}}
    @if ($showTicket && $ultimoCodigo)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full p-6">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 mx-auto bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Venta completada</h3>
                    <p class="text-sm text-gray-500 mt-1">Pedido <span class="font-mono font-bold">{{ $ultimoCodigo }}</span></p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded p-4 mb-4 text-center">
                    <div class="text-sm text-gray-500">TOTAL COBRADO</div>
                    <div class="text-4xl font-bold text-primary-600">€ {{ number_format($ultimoTotal, 2, ',', '.') }}</div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="window.print()"
                            class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white py-3 rounded font-semibold">
                        Imprimir
                    </button>
                    <button wire:click="nuevaVenta"
                            class="bg-primary-600 hover:bg-primary-700 text-white py-3 rounded font-semibold">
                        Nueva venta
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
