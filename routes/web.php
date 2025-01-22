<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('accueil');
});
Route::post('/upload', [MainController::class, 'uploadFiles'])->name('upload.files');
