<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoacaoController;
use App\Http\Controllers\DoadorController;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\ParoquiaController;
use App\Http\Controllers\BeneficiarioController;
use App\Http\Controllers\PublicDonationLookupController;

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('paroquias', ParoquiaController::class);
    Route::resource('users', UserController::class);
    Route::resource('logs', LogController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('items', ItemController::class);
    Route::resource('doacoes', DoacaoController::class);
    Route::get('/doadores/buscar', [DoadorController::class, 'buscar'])->name('doadores.buscar');
    Route::resource('doadores', DoadorController::class);
    Route::resource('beneficiarios', BeneficiarioController::class);
    Route::get('/painel', [PainelController::class, 'index'])->name('painel.dashboard');
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('inprogress', function () {
    return view('inprogress');
})->name('inprogress');
Route::get('finished', function () {
    return view('finished');
})->name('finished');
Route::get('soon', function () {
    return view('soon');
})->name('soon');
Route::get('about', function () {
    return view('about');
})->name('about');
Route::get('seek', PublicDonationLookupController::class)->name('seek');

Auth::routes(['register' => false]);
