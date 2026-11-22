<?php

namespace App\Http\Requests;

use App\Models\Categoria;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransaccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cuenta_id' => ['required', 'exists:cuentas,id'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'tipo' => ['required', Rule::in(['ingreso', 'gasto'])],
            'monto' => ['required', 'numeric', 'gt:0', 'max:999999999999.99', 'decimal:0,2'],
            'fecha' => ['required', 'date'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->hasAny(['categoria_id', 'tipo'])) {
                return;
            }

            $categoria = Categoria::find($this->input('categoria_id'));

            if ($categoria && $categoria->tipo !== $this->input('tipo')) {
                $validator->errors()->add(
                    'categoria_id',
                    "La categoria '{$categoria->nombre}' es de tipo {$categoria->tipo} y no puede "
                    ."usarse en un movimiento de tipo {$this->input('tipo')}."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'max' => 'El campo :attribute no puede superar :max.',
            'numeric' => 'El campo :attribute debe ser un numero.',
            'monto.gt' => 'El monto debe ser mayor que cero.',
            'monto.decimal' => 'El monto admite maximo 2 decimales.',
            'cuenta_id.exists' => 'La cuenta seleccionada no existe.',
            'categoria_id.exists' => 'La categoria seleccionada no existe.',
            'tipo.in' => "El tipo debe ser 'ingreso' o 'gasto'.",
            'fecha.date' => 'La fecha no es valida.',
        ];
    }

    public function attributes(): array
    {
        return [
            'cuenta_id' => 'cuenta',
            'categoria_id' => 'categoria',
            'tipo' => 'tipo',
            'monto' => 'monto',
            'fecha' => 'fecha',
            'descripcion' => 'descripcion',
        ];
    }
}