<?php

use App\Http\Controllers\DashboardInsidenController;
use App\Http\Controllers\DashboardK3Controller;
use App\Http\Controllers\DashboardMutuController;
use App\Http\Controllers\DashboardPpiController;
use App\Http\Controllers\GenerateFileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsidenController;
use App\Http\Controllers\InsidenHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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

Auth::routes([
    "register" => false,
    "password.confirm" => false,
    "password.email" => false,
    "password.reset" => false,
    "password.update" => false,
    "password.request" => false,
]);

/**
 * PERLU LOGIN
 */
// dd(Gate::allows('ppi'));


Route::middleware(["auth"])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/test', function (Request $request) {
        dd("tetr");
        dd(Auth::user());
    });


    /**
     *
     * Inside Route
     *
     */
    Route::get('insiden/dashboard', [DashboardInsidenController::class, 'index'])->name('insiden.dashboard.index');
    Route::post('insiden/dashboard', [DashboardInsidenController::class, 'showChart'])->name('insiden.dashboard');
    Route::get('insiden/data', [InsidenController::class, 'data'])->name('insiden.data');
    Route::get('insiden', [InsidenController::class, 'index'])->name('insiden.index');
    Route::get('insiden/{id}', [InsidenController::class, 'edit'])->name('insiden.edit');
    Route::put('insiden/{id?}', [InsidenController::class, 'update'])->name('insiden.update');
    Route::post('insiden/verif', [InsidenController::class, 'verif'])->name('insiden.verif');
    Route::delete('insiden/{id}', [InsidenController::class, 'destroy'])->name('insiden.destroy');

    /**
     *
     * History Route
     *
     */
    Route::get('history/insiden-view/data', [InsidenHistoryController::class, 'data'])->name('insiden.history.data');
    Route::get('history/insiden-view', [InsidenHistoryController::class, 'index'])->name('insiden.history.index');

    /**
     *
     * Mutu Route
     *
     */
    Route::get('mutu/dashboard', [DashboardMutuController::class, 'index'])->name('mutu.dashboard');
    Route::post('mutu/filter/dashboard', [DashboardMutuController::class, 'showChart'])->name('mutu.filter.dashboard');

    /**
     *
     * Mutu Process Route
     *
     */
    Route::get('mutu/baca/', [DashboardMutuController::class, 'baca'])->name('mutu.baca');
    Route::get('indikator/sub/', [DashboardMutuController::class, 'getSubIndikator'])->name('mutu.indikator.subIndikator');
    Route::get('indikator/unit/', [DashboardMutuController::class, 'getUnit'])->name('mutu.indikator.subIndikator.unit');
    Route::get('generate/mutu/', [GenerateFileController::class, 'index'])->name('mutu.generate.index');
    Route::post('generate/mutu/', [GenerateFileController::class, 'generate'])->name('mutu.generate.file');

    /**
     *
     * K3 Route
     *
     */
    Route::get('dashboard/k3', [DashboardK3Controller::class, 'index'])->name('k3.dashboard');
    Route::post('dashboard/k3/filter', [DashboardK3Controller::class, 'showChart'])->name('k3.filter.dashboard');

    /**
     *
     * PPI Route
     *
     */
    Route::get('dashboard/ppi', [DashboardPpiController::class, 'index'])->name('ppi.dashboard');
    Route::post('dashboard/ppi/filter', [DashboardPpiController::class, 'showChart'])->name('ppi.filter.dashboard');
});

/**
 * OPTIMIZE IN SHARED HOSTING
 * TEST
 */
Route::get('/clear', function () {

    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('optimize:clear');
    return "Config, cache, route, view Cleared!";
});
