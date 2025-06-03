<?php

use App\Http\Controllers\UploadController;
use App\Http\Middleware\Integration\Authenticated;
use Illuminate\Support\Facades\Route;

Route::middleware([Authenticated::class])->group(function () {
    Route::post('files/upload/{uuid?}', [UploadController::class, 'store'])->name('files.upload');
});
