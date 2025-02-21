<?php


use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\PonenteController;
use App\Http\Controllers\PayPalController;

require __DIR__.'/auth.php';

Route::get('/', function () {
    $eventos = \App\Models\Eventos::all();
    $ponentes = \App\Models\Ponente::all();
    return view('welcome', compact('eventos', 'ponentes'));
})->name('home');

Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

Route::get('/eventos', [EventoController::class, 'index'])->name('eventos');
Route::get('/ponentes', [PonenteController::class, 'index'])->name('ponentes');

Route::middleware(['auth'])->group(function () {
    Route::post('/eventos', [EventoController::class, 'crearEvento'])->name('eventos.store');
    Route::delete('/eventos/{id}', [EventoController::class, 'eliminarEvento'])->name('eventos.destroy');

    Route::post('/ponentes', [PonenteController::class, 'crearPonente'])->name('ponentes.store');
    Route::delete('/ponentes/{id}', [PonenteController::class, 'eliminarPonente'])->name('ponentes.destroy');

    Route::get('/inscripciones', [EventoController::class, 'verInscripciones'])->name('inscripciones');
    Route::delete('/inscripciones/{id}', [EventoController::class, 'eliminarInscripcion'])->name('inscripciones.destroy');
});

// Ruta para inscribirse en un evento
Route::post('/eventos/{id}/inscribirse', [EventoController::class, 'inscribirse'])->middleware(['auth'])->name('eventos.inscribirse');

// Ruta para editar el perfil del usuario
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');
});

// Rutas de PayPal
Route::post('/make-payment/{id}/{tipo_inscripcion}', [PayPalController::class, 'handlePayment'])->name('make.payment');
Route::get('/cancel-payment', [PayPalController::class, 'paymentCancel'])->name('cancel.payment');
Route::get('/success-payment/{id}/{tipo_inscripcion}', [PayPalController::class, 'paymentSuccess'])->name('success.payment');

