<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserInviteController;
use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/u/{code}', [UrlController::class, 'redirect'])->name('url.redirect');

Route::get('invite/accept/{token}', [UserInviteController::class, 'accept'])->name('invite.accept');
Route::post('invite/accept/{token}', [UserInviteController::class, 'acceptSubmit'])->name('invite.accept.submit');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::middleware('role:SuperAdmin')
        ->resource('companies', CompanyController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy', 'show']);

    Route::get('invite', [UserInviteController::class, 'index'])->name('invite.index');
    Route::post('invite', [UserInviteController::class, 'store'])->name('invite.store');
    Route::delete('invite/{invite}', [UserInviteController::class, 'destroy'])->name('invite.destroy');

    Route::resource('urls', UrlController::class);
});

require __DIR__ . '/auth.php';
