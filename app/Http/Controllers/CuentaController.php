<?php

namespace App\Http\Controllers;

use App\Http\Requests\CuentaRequest;
use App\Models\Cuenta;

class CuentaController extends Controller
{
    public function index()
    {
        $cuentas = Cuenta::withCount('transacciones')
            ->orderBy('nombre')
            ->paginate(10);

        return view('cuentas.index', compact('cuentas'));
    }

    public function create()
    {
        return view('cuentas.create', [
            'cuenta' => new Cuenta(['saldo' => 0, 'moneda' => 'COP']),
        ]);
    }

    public function store(CuentaRequest $request)
    {
        $datos = $request->validated();
        $datos['saldo'] = $datos['saldo'] ?? 0;
        $datos['moneda'] = $datos['moneda'] ?? 'COP';

        Cuenta::create($datos);

        return redirect()->route('cuentas.index')
            ->with('success', 'Cuenta creada correctamente.');
    }

    public function edit(Cuenta $cuenta)
    {
        return view('cuentas.edit', compact('cuenta'));
    }

    public function update(CuentaRequest $request, Cuenta $cuenta)
    {
        $datos = $request->validated();
        $datos['saldo'] = $datos['saldo'] ?? 0;
        $datos['moneda'] = $datos['moneda'] ?? 'COP';

        $cuenta->update($datos);

        return redirect()->route('cuentas.index')
            ->with('success', 'Cuenta actualizada correctamente.');
    }

    public function destroy(Cuenta $cuenta)
    {
        if ($cuenta->transacciones()->exists()) {
            return redirect()->route('cuentas.index')
                ->with('error', "No se puede eliminar la cuenta «{$cuenta->nombre}» porque tiene movimientos asociados.");
        }

        $cuenta->delete();

        return redirect()->route('cuentas.index')
            ->with('success', 'Cuenta eliminada correctamente.');
    }
}
