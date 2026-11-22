<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Transacciones</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-alertas />

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('transacciones.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition">
                        Nueva transacción
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-300 dark:border-gray-600 text-left">
                                <th class="py-2 pr-4">Fecha</th>
                                <th class="py-2 pr-4">Tipo</th>
                                <th class="py-2 pr-4">Cuenta</th>
                                <th class="py-2 pr-4">Categoría</th>
                                <th class="py-2 pr-4 text-right">Monto</th>
                                <th class="py-2 pr-4">Descripción</th>
                                <th class="py-2 pr-4">Registrado por</th>
                                <th class="py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transacciones as $t)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="py-2 pr-4 whitespace-nowrap">{{ $t->fecha->format('d/m/Y') }}</td>
                                    <td class="py-2 pr-4">
                                        <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $t->tipo === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($t->tipo) }}
                                        </span>
                                    </td>
                                    <td class="py-2 pr-4">{{ $t->cuenta->nombre }}</td>
                                    <td class="py-2 pr-4">{{ $t->categoria->nombre }}</td>
                                    <td class="py-2 pr-4 text-right font-mono {{ $t->tipo === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $t->tipo === 'ingreso' ? '+' : '-' }}$ {{ number_format($t->monto, 2, ',', '.') }}
                                    </td>
                                    <td class="py-2 pr-4">{{ $t->descripcion }}</td>
                                    <td class="py-2 pr-4">{{ $t->creado_por }}</td>
                                    <td class="py-2 text-right whitespace-nowrap">
                                        <a href="{{ route('transacciones.edit', $t) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('transacciones.destroy', $t) }}" class="inline"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta transacción? Se revertirá su efecto en el saldo.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4 text-center text-gray-500">No hay transacciones registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $transacciones->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
