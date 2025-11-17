<?php

use App\Models\Skelbimas;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SkelbimasController;
use App\Http\Controllers\KomentarasController;

// ------------------------------
// PAGRINDINIS PUSLAPIS
// ------------------------------
Route::get('/', function () {
    $skelbimai = Skelbimas::orderBy('id', 'desc')->take(8)->get(); // 8 naujausi
    return view('home', compact('skelbimai'));
});

// ------------------------------
// REGISTRACIJA IR LOGIN
// ------------------------------
Route::get('/register', [UserController::class, 'showRegisterForm']);
Route::post('/register', [UserController::class, 'register']);

Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// ------------------------------
// SKELBIMAI (visiems matomi)
// ------------------------------
Route::get('/skelbimai', [SkelbimasController::class, 'index'])
    ->name('skelbimai.index');



// ------------------------------
// PRIEIGA TIK PRISIJUNGUSIEMS
// ------------------------------
Route::middleware('auth')->group(function () {

    // Kūrimo forma
    Route::get('/skelbimai/kurti', [SkelbimasController::class, 'create']);

    // Kūrimo submit
    Route::post('/skelbimai', [SkelbimasController::class, 'store']);

    // Redagavimo forma
    Route::get('/skelbimai/{id}/redaguoti', [SkelbimasController::class, 'edit']);

    // Redagavimo submit
    Route::put('/skelbimai/{id}', [SkelbimasController::class, 'update']);

    // nuotraukų rūšiavimas
    Route::post('/skelbimai/nuotrauka/{id}/up',   [SkelbimasController::class, 'movePhotoUp'])->name('nuotrauka.up');
    Route::post('/skelbimai/nuotrauka/{id}/down', [SkelbimasController::class, 'movePhotoDown'])->name('nuotrauka.down');

    // nuotraukos trynimas
    Route::delete('/skelbimai/nuotrauka/{id}', [SkelbimasController::class, 'deletePhoto'])
    ->name('nuotrauka.delete');

    // pridėti papildomas nuotraukas
    Route::post('/skelbimai/{id}/nuotraukos', [SkelbimasController::class, 'addPhotos'])
    ->name('nuotrauka.add');

    Route::post('/skelbimai/{id}/komentarai', [KomentarasController::class, 'store'])
    ->name('komentarai.store');

});

Route::get('/skelbimai/mano', [SkelbimasController::class, 'myAds'])
    ->middleware('auth')
    ->name('skelbimai.mano');
// ------------------------------
// VIENO SKELBIMO PERŽIŪRA
// ------------------------------
Route::get('/skelbimai/{id}', [SkelbimasController::class, 'show']);



// ADMIN
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/vartotojai', [AdminController::class, 'users'])
        ->name('admin.users');

    Route::post('/admin/vartotojai/{id}/allow', [AdminController::class, 'allow'])
        ->name('admin.allow');

    Route::post('/admin/vartotojai/{id}/deny', [AdminController::class, 'deny'])
        ->name('admin.deny');

});

// KONTROLIERIUS
Route::middleware(['auth', 'kontrolierius'])->group(function () {
    Route::delete('/skelbimai/{id}/delete', [SkelbimasController::class, 'destroyByKontrolierius'])
        ->name('kontrolierius.delete');
});