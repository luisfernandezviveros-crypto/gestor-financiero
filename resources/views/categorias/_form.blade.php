<form method="POST"
      action="{{ $categoria->exists ? route('categorias.update', $categoria) : route('categorias.store') }}"
      class="space-y-6">
    @csrf
    @if ($categoria->exists)
        @method('PUT')
    @endif

    <div>
        <x-input-label for="nombre" value="Nombre" />
        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full"
                      :value="old('nombre', $categoria->nombre)" required autofocus maxlength="150" />
        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="tipo" value="Tipo" />
        <x-select-input id="tipo" name="tipo" class="mt-1 block w-full" required>
            <option value="ingreso" @selected(old('tipo', $categoria->tipo) === 'ingreso')>Ingreso</option>
            <option value="gasto" @selected(old('tipo', $categoria->tipo) === 'gasto')>Gasto</option>
        </x-select-input>
        <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="descripcion" value="Descripción (opcional)" />
        <x-text-input id="descripcion" name="descripcion" type="text" class="mt-1 block w-full"
                      :value="old('descripcion', $categoria->descripcion)" maxlength="255" />
        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $categoria->exists ? 'Actualizar' : 'Guardar' }}</x-primary-button>
        <a href="{{ route('categorias.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
    </div>
</form>
