<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockMutationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('products', ProductController::class);

    Route::resource('mutations', StockMutationController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('mutations/{id}/approve', [StockMutationController::class, 'approve'])->name('mutations.approve');
    Route::post('mutations/{id}/receive', [StockMutationController::class, 'receive'])->name('mutations.receive');
    Route::post('mutations/{id}/reject', [StockMutationController::class, 'reject'])->name('mutations.reject');
});

require __DIR__.'/auth.php';
