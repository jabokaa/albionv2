<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ItemCategoriaController;
use App\Http\Controllers\CraftController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TransporteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/itens', [ItemController::class, 'index'])->name('itens.index');
Route::get('/itens/{id}/mercado', [ItemController::class, 'mercado'])->whereNumber('id')->name('itens.mercado');
Route::get('/itens/{id}/craft',       [ItemController::class, 'craft'])->whereNumber('id')->name('itens.craft');
Route::post('/itens/{id}/atualizar',  [ItemController::class, 'atualizarPrecos'])->whereNumber('id')->name('itens.atualizar');

Route::get('/transporte', [TransporteController::class, 'index'])->name('transporte.index');
Route::get('/crafting', [CraftController::class, 'index'])->name('crafting.index');

Route::get('/guias/black-market', fn() => view('guias.blackmarket'))->name('guias.blackmarket');

/* ── Admin ────────────────────────────────────────────────── */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::resource('categorias', CategoriaController::class)
             ->except(['show']);
        Route::post('/categorias/{id}/duplicate',      [CategoriaController::class, 'duplicate'])->name('categorias.duplicate');
        Route::post('/categorias/{id}/restore',       [CategoriaController::class, 'restore'])->name('categorias.restore');
        Route::delete('/categorias/{id}/force-delete', [CategoriaController::class, 'forceDestroy'])->name('categorias.force-delete');

        Route::get('/itens',           [ItemCategoriaController::class, 'index'])->name('itens.index');
        Route::get('/itens/busca',     [ItemCategoriaController::class, 'busca'])->name('itens.busca');
        Route::patch('/itens/{item}',  [ItemCategoriaController::class, 'update'])->name('itens.update');
        Route::post('/itens/lote',     [ItemCategoriaController::class, 'updateLote'])->name('itens.lote');
    });
});
