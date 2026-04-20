<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Homepage;
use App\Livewire\News\Index as NewsIndex;

use App\Livewire\Memos\Index as MemosIndex;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard = Homepage
    Route::get('/', Homepage::class)->name('home');
    Route::get('/dashboard', Homepage::class)->name('dashboard');
   // Route::get('/news', NewsIndex::class)->name('news.index');
   Route::get('/news', NewsIndex::class)->name('news.index');


    // Memos list
    Route::get('/memos', MemosIndex::class)->name('memos.index');

});
