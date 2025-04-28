<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SignIn\SignInController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/sign-in', [SignInController::class, 'index'])->name('sign-in');
Route::post('/sign-in', [SignInController::class, 'store'])->name('sign-in.store');

Route::middleware('auth')->as('app.')->group(function () {
    Route::get('', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('files/upload/{uuid?}', [UploadController::class, 'index'])->name('files.upload');
    Route::get('files/{file}/download', [DownloadController::class, 'index'])->name('files.download');
    Route::get('files/folder/{uuid}', [FileController::class, 'index'])->name('files.folder.index');
    Route::post('files/upload/{uuid?}', [UploadController::class, 'store'])->name('files.upload');
    Route::post('files/{uuid?}', [FileController::class, 'store'])->name('files.folder.store');
    Route::put('files/{uuid}/{parent?}', [FileController::class, 'update'])->name('files.a.update');
    Route::delete('files/{uuid}/{parent?}', [FileController::class, 'destroy'])->name('files.a.destroy');

    Route::resource('files', FileController::class);

    Route::resource('users', UserController::class);

    Route::resource('profile', ProfileController::class);
});
