<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    // Главная страница
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Файлы
    Route::prefix('files')->group(function () {
        Route::post('/upload', [FileController::class, 'upload'])->name('files.upload');
        Route::delete('/{file}', [FileController::class, 'destroy'])->name('files.destroy');
        Route::put('/{file}/rename', [FileController::class, 'rename'])->name('files.rename');
        Route::get('/{file}/download', [FileController::class, 'download'])->name('files.download');
        Route::put('/files/{file}/move', [FileController::class, 'move'])->name('files.move');
    });

    // Папки
    Route::prefix('folders')->group(function () {
        Route::post('/', [FolderController::class, 'store'])->name('folders.store');
        Route::put('/{folder}', [FolderController::class, 'update'])->name('folders.update');
        Route::delete('/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');
    });

    // Теги
    Route::post('/files/{file}/tags', [FileController::class, 'updateTags'])->name('files.tags.update');
});