<form method="POST"
      action="{{ $cuenta->exists ? route('cuentas.update', $cuenta) : route('cuentas.store') }}"
      class="space-y-6">
    @csrf
    @if ($cuenta->exists)
        @method('PUT')
    @endif

    <div>
        <x-input-label for="nombre" value="Nombre" />
        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full"
                      :value="old('nombre', $cuenta->nombre)" required autofocus maxlength="150" />
        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="descripcion" value="Descripción (opcional)" />
        <x-text-input id="descripcion" name="descripcion" type="text" class="mt-1 block w-full"
                      :value="old('descripcion', $cuenta->descripcion)" maxlength="255" />
        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="saldo" value="Saldo" />
            <x-text-input id="saldo" name="saldo" type="number" step="0.01" min="0" class="mt-1 block w-full"
                          :value="old('saldo', $cuenta->saldo)" />
            <x-input-error :messages="$errors->get('saldo')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="moneda" value="Moneda" />
            <x-text-input id="moneda" name="moneda" type="text" class="mt-1 block w-full"
                          :value="old('moneda', $cuenta->moneda)" maxlength="10" />
            <x-input-error :messages="$errors->get('moneda')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $cuenta->exists ? 'Actualizar' : 'Guardar' }}</x-primary-button>
        <a href="{{ route('cuentas.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
    </div>
</form>
