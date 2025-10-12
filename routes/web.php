<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'is_admin'])->group(function () {
 Route::get('admin', [AdminController::class, 'index'])->name('admin.dashboard');
 Route::get('users/create', [AdminController::class, 'createUserForm'])->name('admin.users.create');
 Route::get('users', [AdminController::class,'listUser'])->name('admin.users.list');
 Route::post('users', [AdminController::class,'storeUser'])->name('admin.users.store');
 Route::get('users/{user}/edit', [AdminController::class,'editUser'])->name('admin.users.edit');
 Route::put('users/{user}', [AdminController::class,'updateUser'])->name('admin.users.update');
 Route::delete('users/{user}', [AdminController::class,'deleteUser'])->name('admin.users.delete');
});

Route::get('/', function () {return view('welcome');});
Route::get('inprogress', function () {return view('inprogress');})->name('inprogress');
Route::get('finished', function () {return view('finished');})->name('finished');
Route::get('soon', function () {return view('soon');})->name('soon');
Route::get('about', function () {return view('about');})->name('about');
Route::get('seek', function () {return view('seek');})->name('seek');

Auth::routes(['register'=> false]);
