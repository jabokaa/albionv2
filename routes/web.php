<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/itens', [ItemController::class, 'index'])->name('itens.index');
Route::get('/itens/{id}/mercado', [ItemController::class, 'mercado'])->whereNumber('id')->name('itens.mercado');
