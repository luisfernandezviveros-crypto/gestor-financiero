<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Cuentas</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-alertas />

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('cuentas.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition">
                        Nueva cuenta
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-300 dark:border-gray-600 text-left">
                                <th class="py-2 pr-4">Nombre</th>
                                <th class="py-2 pr-4">Descripción</th>
                                <th class="py-2 pr-4 text-right">Saldo actual</th>
                                <th class="py-2 pr-4">Moneda</th>
                                <th class="py-2 pr-4 text-center">Movimientos</th>
                                <th class="py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cuentas as $cuenta)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="py-2 pr-4">{{ $cuenta->nombre }}</td>
                                    <td class="py-2 pr-4">{{ $cuenta->descripcion }}</td>
                                    <td class="py-2 pr-4 text-right font-mono font-semibold">
                                        $ {{ number_format($cuenta->saldo, 2, ',', '.') }}
                                    </td>
                                    <td class="py-2 pr-4">{{ $cuenta->moneda }}</td>
                                    <td class="py-2 pr-4 text-center">{{ $cuenta->transacciones_count }}</td>
                                    <td class="py-2 text-right whitespace-nowrap">
                                        <a href="{{ route('cuentas.edit', $cuenta) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('cuentas.destroy', $cuenta) }}" class="inline"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta cuenta?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-500">No hay cuentas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $cuentas->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
