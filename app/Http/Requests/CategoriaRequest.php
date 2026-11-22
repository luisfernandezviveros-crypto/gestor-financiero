<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'tipo' => ['required', Rule::in(['ingreso', 'gasto'])],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $categoria = $this->route('categoria');

            if ($categoria && ! $validator->errors()->has('tipo')
                && $categoria->tipo !== $this->input('tipo')
                && $categoria->transacciones()->exists()) {
                $validator->errors()->add(
                    'tipo',
                    'No se puede cambiar el tipo: la categoría ya tiene transacciones asociadas.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'max' => 'El campo :attribute no puede superar :max caracteres.',
            'tipo.in' => 'El tipo debe ser «ingreso» o «gasto».',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'tipo' => 'tipo',
            'descripcion' => 'descripción',
        ];
    }
}
