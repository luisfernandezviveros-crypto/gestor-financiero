<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cuenta;
use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GestorFinancieroTest extends TestCase
{
    use RefreshDatabase;

    private function categoria(string $tipo, ?string $nombre = null): Categoria
    {
        return Categoria::create(['nombre' => $nombre ?? ucfirst($tipo).' prueba', 'tipo' => $tipo]);
    }

    private function cuenta(float $saldo, string $nombre = 'Efectivo'): Cuenta
    {
        return Cuenta::create(['nombre' => $nombre, 'saldo' => $saldo]);
    }

    private function datosMovimiento(Cuenta $cuenta, Categoria $categoria, string $tipo, float $monto): array
    {
        return [
            'cuenta_id' => $cuenta->id,
            'categoria_id' => $categoria->id,
            'tipo' => $tipo,
            'monto' => $monto,
            'fecha' => '2026-11-21',
            'descripcion' => 'Prueba',
        ];
    }

    private function usuario(): User
    {
        return User::factory()->create(['name' => 'Tester']);
    }

    public function test_invitados_son_enviados_al_login(): void
    {
        foreach (['categorias.index', 'cuentas.index', 'transacciones.index'] as $ruta) {
            $this->get(route($ruta))->assertRedirect(route('login'));
        }
    }

    public function test_caso_de_sustentacion_200000_mas_150000_menos_20000_da_330000(): void
    {
        $cuenta = $this->cuenta(200000);
        $ingreso = $this->categoria('ingreso');
        $gasto = $this->categoria('gasto');
        $usuario = $this->usuario();

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $ingreso, 'ingreso', 150000))
            ->assertRedirect(route('transacciones.index'));
        $this->assertSame('350000.00', $cuenta->fresh()->saldo);

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $gasto, 'gasto', 20000))
            ->assertRedirect(route('transacciones.index'));
        $this->assertSame('330000.00', $cuenta->fresh()->saldo);

        $this->assertSame('Tester', Transaccion::first()->creado_por);
    }

    public function test_editar_revierte_el_efecto_anterior_aunque_cambie_cuenta_y_tipo(): void
    {
        $a = $this->cuenta(200000, 'A');
        $b = $this->cuenta(50000, 'B');
        $ingreso = $this->categoria('ingreso');
        $gasto = $this->categoria('gasto');
        $usuario = $this->usuario();

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($a, $ingreso, 'ingreso', 100000));
        $this->assertSame('300000.00', $a->fresh()->saldo);

        $t = Transaccion::first();

        $this->actingAs($usuario)
            ->put(route('transacciones.update', $t), $this->datosMovimiento($b, $gasto, 'gasto', 30000))
            ->assertRedirect(route('transacciones.index'));

        $this->assertSame('200000.00', $a->fresh()->saldo);
        $this->assertSame('20000.00', $b->fresh()->saldo);
    }

    public function test_editar_el_monto_en_la_misma_cuenta_aplica_solo_la_diferencia(): void
    {
        $cuenta = $this->cuenta(200000);
        $ingreso = $this->categoria('ingreso');
        $usuario = $this->usuario();

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $ingreso, 'ingreso', 100000));
        $t = Transaccion::first();

        $this->actingAs($usuario)
            ->put(route('transacciones.update', $t), $this->datosMovimiento($cuenta, $ingreso, 'ingreso', 120000));

        $this->assertSame('320000.00', $cuenta->fresh()->saldo);
    }

    public function test_eliminar_una_transaccion_revierte_su_efecto(): void
    {
        $cuenta = $this->cuenta(200000);
        $gasto = $this->categoria('gasto');
        $usuario = $this->usuario();

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $gasto, 'gasto', 20000));
        $this->assertSame('180000.00', $cuenta->fresh()->saldo);

        $this->actingAs($usuario)->delete(route('transacciones.destroy', Transaccion::first()));

        $this->assertSame('200000.00', $cuenta->fresh()->saldo);
        $this->assertDatabaseCount('transacciones', 0);
    }

    public function test_no_se_permite_un_gasto_mayor_al_saldo(): void
    {
        $cuenta = $this->cuenta(10000);
        $gasto = $this->categoria('gasto');

        $this->actingAs($this->usuario())
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $gasto, 'gasto', 10000.01))
            ->assertSessionHasErrors('monto');

        $this->assertSame('10000.00', $cuenta->fresh()->saldo);
        $this->assertDatabaseCount('transacciones', 0);
    }

    public function test_un_gasto_igual_al_saldo_si_se_permite(): void
    {
        $cuenta = $this->cuenta(10000);
        $gasto = $this->categoria('gasto');

        $this->actingAs($this->usuario())
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $gasto, 'gasto', 10000))
            ->assertSessionHasNoErrors();

        $this->assertSame('0.00', $cuenta->fresh()->saldo);
    }

    public function test_no_se_aceptan_montos_cero_o_negativos(): void
    {
        $cuenta = $this->cuenta(100000);
        $ingreso = $this->categoria('ingreso');
        $usuario = $this->usuario();

        foreach ([0, -5000] as $monto) {
            $this->actingAs($usuario)
                ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $ingreso, 'ingreso', $monto))
                ->assertSessionHasErrors('monto');
        }

        $this->assertDatabaseCount('transacciones', 0);
        $this->assertSame('100000.00', $cuenta->fresh()->saldo);
    }

    public function test_la_categoria_debe_coincidir_con_el_tipo_del_movimiento(): void
    {
        $cuenta = $this->cuenta(100000);
        $ingreso = $this->categoria('ingreso');
        $gasto = $this->categoria('gasto');
        $usuario = $this->usuario();

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $ingreso, 'gasto', 1000))
            ->assertSessionHasErrors('categoria_id');

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $gasto, 'ingreso', 1000))
            ->assertSessionHasErrors('categoria_id');

        $this->assertDatabaseCount('transacciones', 0);
    }

    public function test_el_formulario_conserva_los_datos_cuando_falla_la_validacion(): void
    {
        $cuenta = $this->cuenta(100000);
        $ingreso = $this->categoria('ingreso');

        $this->actingAs($this->usuario())
            ->from(route('transacciones.create'))
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $ingreso, 'ingreso', 0))
            ->assertRedirect(route('transacciones.create'))
            ->assertSessionHasInput('descripcion', 'Prueba');
    }

    public function test_no_se_puede_eliminar_una_cuenta_con_transacciones(): void
    {
        $cuenta = $this->cuenta(100000);
        $ingreso = $this->categoria('ingreso');
        $usuario = $this->usuario();

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $ingreso, 'ingreso', 1000));

        $this->actingAs($usuario)
            ->delete(route('cuentas.destroy', $cuenta))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('cuentas', ['id' => $cuenta->id]);
    }

    public function test_no_se_puede_eliminar_una_categoria_con_transacciones(): void
    {
        $cuenta = $this->cuenta(100000);
        $ingreso = $this->categoria('ingreso');
        $usuario = $this->usuario();

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $ingreso, 'ingreso', 1000));

        $this->actingAs($usuario)
            ->delete(route('categorias.destroy', $ingreso))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categorias', ['id' => $ingreso->id]);
    }

    public function test_se_puede_eliminar_cuenta_y_categoria_sin_transacciones(): void
    {
        $usuario = $this->usuario();
        $cuenta = $this->cuenta(0);
        $categoria = $this->categoria('gasto');

        $this->actingAs($usuario)->delete(route('cuentas.destroy', $cuenta))->assertSessionHas('success');
        $this->actingAs($usuario)->delete(route('categorias.destroy', $categoria))->assertSessionHas('success');

        $this->assertDatabaseCount('cuentas', 0);
        $this->assertDatabaseCount('categorias', 0);
    }

    public function test_no_se_puede_borrar_un_ingreso_si_el_saldo_quedaria_negativo(): void
    {
        $cuenta = $this->cuenta(0);
        $ingreso = $this->categoria('ingreso');
        $gasto = $this->categoria('gasto');
        $usuario = $this->usuario();

        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $ingreso, 'ingreso', 100000));
        $this->actingAs($usuario)
            ->post(route('transacciones.store'), $this->datosMovimiento($cuenta, $gasto, 'gasto', 90000));

        $ingresoRegistrado = Transaccion::where('tipo', 'ingreso')->first();

        $this->actingAs($usuario)
            ->delete(route('transacciones.destroy', $ingresoRegistrado))
            ->assertSessionHasErrors('monto');

        $this->assertSame('10000.00', $cuenta->fresh()->saldo);
        $this->assertDatabaseCount('transacciones', 2);
    }
}
