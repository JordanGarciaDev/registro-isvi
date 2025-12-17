<?php

use App\Http\Controllers\BrancheController;
use App\Http\Controllers\CostsController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShiftsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\ParametrizationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\ZoneController;

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

Route::get('/clear-cache', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('config:cache');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');

    return 'Caches, config, routes y views limpiados correctamente.';
});

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('turnos', ShiftsController::class);
    Route::post('turnos/generar', [ShiftsController::class, 'generateResults'])->name('turnos.generateResults');
    Route::post('trabajador-datos/', [ShiftsController::class, 'calcularPorTrabajador']);
    Route::get('turnos/ver/programaciones', [ShiftsController::class, 'getShifts'])->name('turnos.getShifts');
    Route::get('/turnos/{id}/detalle', [ShiftsController::class, 'turnDetail']);
    Route::get('/turnos-individuales/{id}/detalle', [ShiftsController::class, 'individualTurnDetail']);
    Route::get('/personal/{id}/detalles', [ShiftsController::class, 'viewDetails'])->name('personal.detalles');
    Route::get('eventos', [EventController::class, 'index']);
    Route::resource('personal', WorkerController::class);
    Route::post('/importar-personal', [WorkerController::class, 'personalImport'])->name('worker.import');
    Route::resource('ubicaciones/zonas', ZoneController::class);
    Route::resource('ubicaciones/regiones', BrancheController::class);
    Route::resource('clientes', CustomerController::class);
    
    // Exports
    Route::get('/zonas/export', [ZoneController::class, 'export'])->name('zonas.export');
    Route::get('/usuarios/export', [UserController::class, 'export'])->name('usuarios.export');

    Route::middleware('can:manage-system')->group(function () {
        Route::resource('novedades', ObservationController::class);
        Route::post('/novedades-reportes', [ObservationController::class, 'generateReport'])->name('novedades.reportes');
        Route::resource('usuarios', UserController::class);
        Route::resource('parametrizacion', ParametrizationController::class);
    });

    Route::get('perfil', [MyProfileController::class, 'index'])->name('profile.index');
    Route::put('perfil', [MyProfileController::class, 'update'])->name('profile.update');
    Route::get('/get-zones-by-region/{id}', [WorkerController::class, 'getZonesByRegion']);

    Route::resource('programaciones', ScheduleController::class)->parameters([
        'programaciones' => 'schedule'
    ]);
});
