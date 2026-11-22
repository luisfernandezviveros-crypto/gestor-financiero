<form method="POST"
      action="{{ $transaccion->exists ? route('transacciones.update', $transaccion) : route('transacciones.store') }}"
      class="space-y-6">
    @csrf
    @if ($transaccion->exists)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="tipo" value="Tipo" />
            <x-select-input id="tipo" name="tipo" class="mt-1 block w-full" required>
                <option value="ingreso" @selected(old('tipo', $transaccion->tipo) === 'ingreso')>Ingreso</option>
                <option value="gasto" @selected(old('tipo', $transaccion->tipo) === 'gasto')>Gasto</option>
            </x-select-input>
            <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="fecha" value="Fecha" />
            <x-text-input id="fecha" name="fecha" type="date" class="mt-1 block w-full"
                          :value="old('fecha', $transaccion->fecha?->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="cuenta_id" value="Cuenta" />
            <x-select-input id="cuenta_id" name="cuenta_id" class="mt-1 block w-full" required>
                <option value="">-- Seleccione una cuenta --</option>
                @foreach ($cuentas as $cuenta)
                    <option value="{{ $cuenta->id }}" @selected((string) old('cuenta_id', $transaccion->cuenta_id) === (string) $cuenta->id)>
                        {{ $cuenta->nombre }} (saldo $ {{ number_format($cuenta->saldo, 2, ',', '.') }})
                    </option>
                @endforeach
            </x-select-input>
            <x-input-error :messages="$errors->get('cuenta_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="categoria_id" value="Categoría" />
            <x-select-input id="categoria_id" name="categoria_id" class="mt-1 block w-full" required>
                <option value="">-- Seleccione una categoría --</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" data-tipo="{{ $categoria->tipo }}"
                            @selected((string) old('categoria_id', $transaccion->categoria_id) === (string) $categoria->id)>
                        {{ $categoria->nombre }} ({{ $categoria->tipo }})
                    </option>
                @endforeach
            </x-select-input>
            <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="monto" value="Monto" />
        <x-text-input id="monto" name="monto" type="number" step="0.01" min="0.01" class="mt-1 block w-full"
                      :value="old('monto', $transaccion->monto)" required />
        <x-input-error :messages="$errors->get('monto')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="descripcion" value="Descripción (opcional)" />
        <x-text-input id="descripcion" name="descripcion" type="text" class="mt-1 block w-full"
                      :value="old('descripcion', $transaccion->descripcion)" maxlength="255" />
        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $transaccion->exists ? 'Actualizar' : 'Guardar' }}</x-primary-button>
        <a href="{{ route('transacciones.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
    </div>
</form>

<script>
    (function () {
        const tipo = document.getElementById('tipo');
        const categoria = document.getElementById('categoria_id');

        function filtrarCategorias() {
            Array.from(categoria.options).forEach(function (opcion) {
                if (!opcion.dataset.tipo) return;
                const coincide = opcion.dataset.tipo === tipo.value;
                opcion.hidden = !coincide;
                opcion.disabled = !coincide;
                if (!coincide && opcion.selected) categoria.value = '';
            });
        }

        tipo.addEventListener('change', filtrarCategorias);
        filtrarCategorias();
    })();
</script>
