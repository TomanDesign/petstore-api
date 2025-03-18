<?php

use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

// Set root (/) route to PetController@index
Route::get('/', [PetController::class, 'index'])->name('pets.index');

// Set routes for PetController
Route::get('/pets', [PetController::class, 'index'])->name('pets.index');
Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
Route::get('/pets/{id}/edit', [PetController::class, 'edit'])->name('pets.edit');
Route::put('/pets/{id}', [PetController::class, 'update'])->name('pets.update');
Route::delete('/pets/{id}', [PetController::class, 'destroy'])->name('pets.destroy');