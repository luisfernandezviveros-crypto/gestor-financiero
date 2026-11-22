<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestor Financiero
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-alertas />

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Saldo por cuenta</h3>

                @forelse ($cuentas as $cuenta)
                    <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 py-2">
                        <span>{{ $cuenta->nombre }}</span>
                        <span class="font-mono font-semibold">
                            $ {{ number_format($cuenta->saldo, 2, ',', '.') }} {{ $cuenta->moneda }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500">Aún no hay cuentas registradas.</p>
                @endforelse
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Últimos movimientos</h3>

                @forelse ($recientes as $t)
                    <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 py-2 text-sm">
                        <span>
                            {{ $t->fecha->format('d/m/Y') }} · {{ $t->cuenta->nombre }} · {{ $t->categoria->nombre }}
                        </span>
                        <span class="font-mono {{ $t->tipo === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $t->tipo === 'ingreso' ? '+' : '-' }}$ {{ number_format($t->monto, 2, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500">Aún no hay transacciones registradas.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
