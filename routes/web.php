<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaccionController;
use App\Models\Cuenta;
use App\Models\Transaccion;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CuentaController;
Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'cuentas' => Cuenta::orderBy('nombre')->get(),
            'recientes' => Transaccion::with(['cuenta', 'categoria'])
                ->orderByDesc('fecha')->orderByDesc('id')->take(5)->get(),
        ]);
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categorias', CategoriaController::class)
        ->parameters(['categorias' => 'categoria'])
        ->except('show');

    Route::resource('cuentas', CuentaController::class)
        ->parameters(['cuentas' => 'cuenta'])
        ->except('show');

    Route::resource('transacciones', TransaccionController::class)
        ->parameters(['transacciones' => 'transaccion'])
        ->except('show');
});

Route::resource('cuentas', CuentaController::class)
    ->parameters(['cuentas' => 'cuenta'])
    ->except('show');

require __DIR__.'/auth.php';
