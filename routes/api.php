<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PonenteController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\InscripcionController;

// API DE PONENTES
Route::get('/ponentes', [PonenteController::class, 'mostrarPonentes']);
Route::post('/ponentes', [PonenteController::class, 'crearPonente']);
Route::delete('/ponentes/{id}', [PonenteController::class, 'eliminarPonente']);

// API DE EVENTOS
Route::get('/eventos', [EventoController::class, 'mostrarEventos']);
Route::post('/eventos', [EventoController::class, 'crearEvento']);
Route::delete('/eventos/{id}', [EventoController::class, 'eliminarEvento']);

// API DE USUARIOS
Route::get('/usuarios', [UsuarioController::class, 'mostrarUsuarios']);

// API DE PAGOS
Route::get('/pagos', [PagoController::class, 'mostrarPagos']);

// API DE INSCRIPCIONES
Route::post('/inscripciones', [InscripcionController::class, 'crearInscripcion']);
Route::delete('/inscripciones/{id}', [InscripcionController::class, 'eliminarInscripcion']);