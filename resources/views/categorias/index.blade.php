<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Categorías</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-alertas />

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('categorias.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition">
                        Nueva categoría
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-300 dark:border-gray-600 text-left">
                                <th class="py-2 pr-4">Nombre</th>
                                <th class="py-2 pr-4">Tipo</th>
                                <th class="py-2 pr-4">Descripción</th>
                                <th class="py-2 pr-4 text-center">Transacciones</th>
                                <th class="py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categorias as $categoria)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="py-2 pr-4">{{ $categoria->nombre }}</td>
                                    <td class="py-2 pr-4">
                                        <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $categoria->tipo === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($categoria->tipo) }}
                                        </span>
                                    </td>
                                    <td class="py-2 pr-4">{{ $categoria->descripcion }}</td>
                                    <td class="py-2 pr-4 text-center">{{ $categoria->transacciones_count }}</td>
                                    <td class="py-2 text-right whitespace-nowrap">
                                        <a href="{{ route('categorias.edit', $categoria) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('categorias.destroy', $categoria) }}" class="inline"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-3 text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">No hay categorías registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $categorias->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
