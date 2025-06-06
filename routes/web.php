<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/welcome', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
// Default routes

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');


Route::get('register', function () {
    return Inertia::render('Register');
});
//test
// Route::get('test', function(){
//     return Inertia::render('Posts/Test');
// });

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // post routes
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::post('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Comment routes
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    //Like routes
    Route::post('/posts/{post}/like', [LikeController::class, 'toggleLike'])->name('posts.like');

    // notifications routes
    Route::get('/api/notifications', [NotificationController::class, 'index']);
    Route::post('/api/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
});
// search route
Route::get('/posts/search', [SearchController::class, 'search'])->name('posts.search');
//search posts of a single user
Route::get('/users/{user}/posts/search', [SearchController::class, 'searchSingleUser'])->name('users.posts.search');
// single post route
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
// posts of a user route
Route::get('/users/{user}/posts', [PostController::class, 'userPosts'])->name('users.posts');
// posts of a tag
Route::get('/tags/{tag}/posts', [PostController::class, 'tagPosts'])->name('tags.posts');
//posts of a tag of a user
Route::get('/users/{user}/tags/{tag}/posts', [PostController::class, 'tagUserPosts'])
    ->name('users.tags.posts');


// Authentication routes (from Laravel Breeze)
require __DIR__ . '/auth.php';
