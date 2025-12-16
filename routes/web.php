<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\TopicController;


Route::get('/', function () {
    return redirect()->route('login');
});


require __DIR__.'/auth.php';


Route::middleware('auth')->group(function () {

    /*Dashboard & Explore*/
    Route::get('/dashboard', [PostController::class, 'index'])->name('dashboard');
    Route::get('/explore', [PostController::class, 'explore'])->name('explore');
    Route::get('/topics/{topic:name}', [TopicController::class, 'show'])->name('topics.show');

    /*Posts*/
    Route::resource('posts', PostController::class)->except(['edit','update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    /*Profile*/
    Route::get('/profile/u/{username}', [ProfileController::class, 'showByUsername'])
        ->name('profile.username');

    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    /*Search*/
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    /*Post Interactions (AJAX)*/
    Route::post('/posts/{post}/like', [InteractionController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/upvote', [InteractionController::class, 'toggleUpvote'])->name('posts.upvote');

    /*Comments (AJAX)*/
    Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{comment}/like', [CommentController::class, 'toggleLike'])->name('comments.like');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/post/{post}', [PostController::class, 'show'])
    ->name('posts.show');

    Route::post('/user/{user}/follow', [FollowController::class, 'toggle'])->name('user.follow');

    Route::get('/about-us', function () {
        return view('pages.about');
    })->name('about');

    Route::get('/contact', function () {
        return view('pages.contact');
    })->name('contact');
});
