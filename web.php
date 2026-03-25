<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;

// Anasayfa (Oda oluşturma ekranı)
Route::get('/', [TeamController::class, 'index'])->name('home');

// Yeni oda oluşturma işlemi (Form gönderildiğinde)
Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');

// Odanın içi (Örn: /besiktas-123)
Route::get('/{slug}', [TeamController::class, 'show'])->name('teams.show');

// Odaya kullanıcı adıyla katılma işlemi
Route::post('/{slug}/join', [TeamController::class, 'join'])->name('teams.join');

// Oy verme işlemi (Yeni Eklendi - JavaScript'ten çağrılacak)
Route::post('/{slug}/vote', [TeamController::class, 'submitVote'])->name('teams.vote');

// Oyları açma ve sıfırlama işlemleri
Route::post('/{slug}/reveal', [TeamController::class, 'revealVotes'])->name('teams.reveal');
Route::post('/{slug}/reset', [TeamController::class, 'resetVotes'])->name('teams.reset');