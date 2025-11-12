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
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\ParoquiaController;
use App\Http\Controllers\BeneficiarioController;
use App\Http\Controllers\PublicDonationLookupController;
use App\Http\Controllers\DistribuicaoController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PublicReportController;

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('paroquias', ParoquiaController::class);
    Route::resource('users', UserController::class);
    Route::resource('logs', LogController::class);
    Route::resource('events', EventController::class)->except(['show']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('itens', ItemController::class);
    Route::get('doacoes/relatorios', [DoacaoController::class, 'report'])->name('doacoes.relatorios');
    Route::get('doacoes/relatorios/export', [DoacaoController::class, 'exportCsv'])->name('doacoes.relatorios.export');
    Route::resource('doacoes', DoacaoController::class)->parameters(['doacoes' => 'doacao']);
    Route::get('/doadores/buscar', [DoadorController::class, 'buscar'])->name('doadores.buscar');
    Route::resource('doadores', DoadorController::class)->parameter('doadores', 'doador');
    Route::resource('beneficiarios', BeneficiarioController::class);
    Route::get('/painel', [PainelController::class, 'index'])->name('painel.dashboard');
    Route::get('estoque', [EstoqueController::class, 'index'])->name('estoque.index');
    Route::get('estoque/{estoque}', [EstoqueController::class, 'show'])->name('estoque.show');
    Route::get('distribuicoes', [DistribuicaoController::class, 'index'])->name('distribuicoes.index');
    Route::get('distribuicoes/relatorios', [DistribuicaoController::class, 'report'])->name('distribuicoes.relatorios');
    Route::get('distribuicoes/relatorios/export', [DistribuicaoController::class, 'exportCsv'])->name('distribuicoes.relatorios.export');
    Route::get('distribuicoes/create', [DistribuicaoController::class, 'create'])->name('distribuicoes.create');
    Route::post('distribuicoes', [DistribuicaoController::class, 'store'])->name('distribuicoes.store');
    Route::get('distribuicoes/{distribuicao}', [DistribuicaoController::class, 'show'])->name('distribuicoes.show');
    Route::get('distribuicoes/{distribuicao}/edit', [DistribuicaoController::class, 'edit'])->name('distribuicoes.edit');
    Route::put('distribuicoes/{distribuicao}', [DistribuicaoController::class, 'update'])->name('distribuicoes.update');
    Route::get('/change-password', [UserController::class, 'changePassword'])->name('password.change')->middleware('auth');
    Route::post('/update-password', [UserController::class, 'updatePassword'])->name('password.update')->middleware('auth');

});

Route::get('/', function () {
    return view('welcome');
});
Route::get('reports', [PublicReportController::class, 'index'])->name('reports.general');
Route::get('events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('soon', [EventController::class, 'soon'])->name('soon');
Route::get('inprogress', [EventController::class, 'inProgress'])->name('inprogress');
Route::get('finished', [EventController::class, 'finished'])->name('finished');
Route::get('about', function () {
    return view('about');
})->name('about');
Route::get('seek', PublicDonationLookupController::class)->name('seek');

Auth::routes(['register' => false]);