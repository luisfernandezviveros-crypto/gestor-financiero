<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\User;
use App\Services\TransaccionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(TransaccionService $servicio): void
    {
        $usuario = User::firstOrCreate(
            ['email' => 'demo@gestorfinanciero.test'],
            ['name' => 'Usuario Demo', 'password' => Hash::make('password123')]
        );

        $salario = Categoria::create(['nombre' => 'Salario', 'tipo' => 'ingreso', 'descripcion' => 'Pago mensual por trabajo']);
        $freelance = Categoria::create(['nombre' => 'Trabajos freelance', 'tipo' => 'ingreso', 'descripcion' => 'Ingresos extra']);
        $alimentacion = Categoria::create(['nombre' => 'Alimentación', 'tipo' => 'gasto', 'descripcion' => 'Mercado y restaurantes']);
        $transporte = Categoria::create(['nombre' => 'Transporte', 'tipo' => 'gasto', 'descripcion' => 'Bus, taxi y gasolina']);
        $servicios = Categoria::create(['nombre' => 'Servicios públicos', 'tipo' => 'gasto', 'descripcion' => 'Agua, luz, internet']);

        $efectivo = Cuenta::create(['nombre' => 'Efectivo', 'descripcion' => 'Dinero en mano', 'saldo' => 200000, 'moneda' => 'COP']);
        $bancolombia = Cuenta::create(['nombre' => 'Cuenta de ahorros Bancolombia', 'descripcion' => 'Cuenta principal', 'saldo' => 500000, 'moneda' => 'COP']);
        $nequi = Cuenta::create(['nombre' => 'Nequi', 'descripcion' => 'Billetera digital', 'saldo' => 100000, 'moneda' => 'COP']);

        $nombre = $usuario->name;

        $servicio->registrar([
            'cuenta_id' => $efectivo->id, 'categoria_id' => $salario->id, 'tipo' => 'ingreso',
            'monto' => 150000, 'fecha' => '2026-11-03', 'descripcion' => 'Pago quincena',
        ], $nombre);

        $servicio->registrar([
            'cuenta_id' => $efectivo->id, 'categoria_id' => $alimentacion->id, 'tipo' => 'gasto',
            'monto' => 20000, 'fecha' => '2026-11-05', 'descripcion' => 'Mercado de la semana',
        ], $nombre);

        $servicio->registrar([
            'cuenta_id' => $bancolombia->id, 'categoria_id' => $freelance->id, 'tipo' => 'ingreso',
            'monto' => 300000, 'fecha' => '2026-11-08', 'descripcion' => 'Proyecto de página web',
        ], $nombre);

        $servicio->registrar([
            'cuenta_id' => $bancolombia->id, 'categoria_id' => $transporte->id, 'tipo' => 'gasto',
            'monto' => 80000, 'fecha' => '2026-11-10', 'descripcion' => 'Recarga de transporte',
        ], $nombre);

        $servicio->registrar([
            'cuenta_id' => $nequi->id, 'categoria_id' => $servicios->id, 'tipo' => 'gasto',
            'monto' => 45000, 'fecha' => '2026-11-12', 'descripcion' => 'Factura de internet',
        ], $nombre);
    }
}
