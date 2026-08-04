<?php

use App\Http\Controllers\Api\Admin\AdminContentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicSite\AboutController;
use App\Http\Controllers\Api\PublicSite\ArticleController;
use App\Http\Controllers\Api\PublicSite\BookController;
use App\Http\Controllers\Api\PublicSite\BuildTomorrowController;
use App\Http\Controllers\Api\PublicSite\ContactController;
use App\Http\Controllers\Api\PublicSite\HomeController;
use App\Http\Controllers\Api\PublicSite\ImpactController;
use App\Http\Controllers\Api\PublicSite\JournalController;
use App\Http\Controllers\Api\PublicSite\LaunchController;
use App\Http\Controllers\Api\PublicSite\MediaController;
use App\Http\Controllers\Api\PublicSite\MentorshipController;
use App\Http\Controllers\Api\PublicSite\NewsletterController;
use App\Http\Controllers\Api\PublicSite\PillarController;
use App\Http\Controllers\Api\PublicSite\SpeakingController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'data' => [
        'status' => 'ok',
        'service' => 'femiowoyele-api',
        'timestamp' => now()->toIso8601String(),
    ],
]));

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::get('/home', HomeController::class);
Route::get('/about', AboutController::class);
Route::get('/pillars', [PillarController::class, 'index']);
Route::get('/pillars/{pillar:slug}', [PillarController::class, 'show']);
Route::get('/research-ideas', [ArticleController::class, 'index']);
Route::get('/research-ideas/{article:slug}', [ArticleController::class, 'show']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book:slug}', [BookController::class, 'show']);
Route::get('/launch', LaunchController::class);
Route::get('/build-tomorrow', [BuildTomorrowController::class, 'index']);
Route::get('/build-tomorrow/{section}', [BuildTomorrowController::class, 'show']);
Route::get('/speaking', SpeakingController::class);
Route::get('/mentorship', MentorshipController::class);
Route::get('/impact', ImpactController::class);
Route::get('/media', MediaController::class);
Route::get('/journal', [JournalController::class, 'index']);
Route::get('/journal/{journalEntry:slug}', [JournalController::class, 'show']);
Route::post('/contact', ContactController::class)->middleware('throttle:contact');
Route::post('/newsletter/subscribe', NewsletterController::class)->middleware('throttle:newsletter');

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'can:manage-content'])
    ->group(function (): void {
        Route::get('/overview', [AdminContentController::class, 'overview']);
        Route::get('/{resource}', [AdminContentController::class, 'index'])->whereIn('resource', AdminContentController::resources());
        Route::post('/{resource}', [AdminContentController::class, 'store'])->whereIn('resource', AdminContentController::resources());
        Route::get('/{resource}/{id}', [AdminContentController::class, 'show'])->whereIn('resource', AdminContentController::resources());
        Route::match(['put', 'patch'], '/{resource}/{id}', [AdminContentController::class, 'update'])->whereIn('resource', AdminContentController::resources());
        Route::delete('/{resource}/{id}', [AdminContentController::class, 'destroy'])->whereIn('resource', AdminContentController::resources());
    });
