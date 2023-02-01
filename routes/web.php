<?php

use App\Http\Controllers\DashboardMutuController;
use App\Http\Controllers\InsidenController;
use App\Http\Controllers\InsidenHistoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Insiden Route
Route::get('insiden/data', [InsidenController::class, 'data'])->name('insiden.data');
Route::get('insiden', [InsidenController::class, 'index'])->name('insiden.index');
Route::get('insiden/{id}', [InsidenController::class, 'edit'])->name('insiden.edit');
Route::put('insiden/{id?}', [InsidenController::class, 'update'])->name('insiden.update');
Route::post('insiden/verif', [InsidenController::class, 'verif'])->name('insiden.verif');
Route::delete('insiden/{id}', [InsidenController::class, 'destroy'])->name('insiden.destroy');

// History Route
Route::get('history/insiden-view/data', [InsidenHistoryController::class, 'data'])->name('insiden.history.data');
Route::get('history/insiden-view', [InsidenHistoryController::class, 'index'])->name('insiden.history.index');

// Mutu
Route::get('mutu/dashboard', [DashboardMutuController::class, 'index'])->name('mutu.dashboard');
