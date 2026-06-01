<?php

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
