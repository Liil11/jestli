<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LikeController;


Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');
    Route::resource('posts', PostController::class)->except(['edit','update']);

// add this:
    Route::get('/profile/{user}', function ($user) {
        $user = \App\Models\User::findOrFail($user);
        return view('profile.show', compact('user'));
    })->name('profile.show');
});
// Dashboard
Route::get('/dashboard', [PostController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// Explore
Route::get('/explore', [PostController::class, 'explore'])
    ->middleware(['auth'])
    ->name('explore');

// Create post
Route::get('/posts/create', [PostController::class, 'create'])
    ->middleware(['auth'])
    ->name('posts.create');

// Show user's posts (profile)
Route::get('/profile/{user}', [ProfileController::class, 'show'])
    ->middleware(['auth'])
    ->name('profile.show');

// Edit profile
Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])
    ->middleware(['auth'])
    ->name('profile.edit');

// Search
Route::get('/search', [SearchController::class, 'index'])
    ->middleware(['auth'])
    ->name('search');

Route::middleware(['auth'])->group(function () {
    Route::post('/posts/{id}/like', [LikeController::class, 'toggle'])
        ->name('posts.like');
});

require __DIR__.'/auth.php';
