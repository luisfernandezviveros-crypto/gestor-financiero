<?php

namespace App\Services;

use App\Models\Cuenta;
use App\Models\Transaccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaccionService
{
    public function registrar(array $datos, string $usuario): Transaccion
    {
        return DB::transaction(function () use ($datos, $usuario) {
            $cuenta = Cuenta::lockForUpdate()->findOrFail($datos['cuenta_id']);

            $this->ajustar($cuenta, $this->efecto($datos['tipo'], $datos['monto']));

            return Transaccion::create($datos + ['creado_por' => $usuario]);
        });
    }

    public function actualizar(Transaccion $transaccion, array $datos): Transaccion
    {
        return DB::transaction(function () use ($transaccion, $datos) {
            $cuentaVieja = (int) $transaccion->cuenta_id;
            $cuentaNueva = (int) $datos['cuenta_id'];

            $cuentas = Cuenta::whereIn('id', array_unique([$cuentaVieja, $cuentaNueva]))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $efectoViejo = $this->efecto($transaccion->tipo, $transaccion->monto);
            $efectoNuevo = $this->efecto($datos['tipo'], $datos['monto']);

            if ($cuentaVieja === $cuentaNueva) {
                $this->ajustar($cuentas[$cuentaVieja], $efectoNuevo - $efectoViejo);
            } else {
                $this->ajustar($cuentas[$cuentaVieja], -$efectoViejo);
                $this->ajustar($cuentas[$cuentaNueva], $efectoNuevo);
            }

            $transaccion->update($datos);

            return $transaccion;
        });
    }

    public function eliminar(Transaccion $transaccion): void
    {
        DB::transaction(function () use ($transaccion) {
            $cuenta = Cuenta::lockForUpdate()->findOrFail($transaccion->cuenta_id);

            $this->ajustar($cuenta, -$this->efecto($transaccion->tipo, $transaccion->monto));

            $transaccion->delete();
        });
    }

    private function efecto(string $tipo, string|int|float $monto): int
    {
        $centavos = $this->aCentavos($monto);

        return $tipo === 'ingreso' ? $centavos : -$centavos;
    }

    private function ajustar(Cuenta $cuenta, int $delta): void
    {
        $actual = $this->aCentavos($cuenta->saldo);
        $nuevo = $actual + $delta;

        if ($nuevo < 0) {
            throw ValidationException::withMessages([
                'monto' => sprintf(
                    'Saldo insuficiente en la cuenta «%s»: saldo disponible $ %s, y la operación lo dejaría en $ %s.',
                    $cuenta->nombre,
                    number_format($actual / 100, 2, ',', '.'),
                    number_format($nuevo / 100, 2, ',', '.')
                ),
            ]);
        }

        $cuenta->saldo = number_format($nuevo / 100, 2, '.', '');
        $cuenta->save();
    }

    private function aCentavos(string|int|float $valor): int
    {
        return (int) round(((float) $valor) * 100);
    }
}
