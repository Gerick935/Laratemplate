<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('accueil');
});
Route::post('/upload', [FileUploadController::class, 'uploadFiles'])->name('upload.files');
