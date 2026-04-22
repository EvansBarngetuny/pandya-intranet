<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Homepage;
use App\Livewire\Memos\Index as MemoIndex;
use App\Livewire\Memos\CreateMemo;
use App\Livewire\Memos\ShowMemo;
use App\Livewire\Memos\EditMemo;
use App\Livewire\Memos\PendingAcknowledgment;
use App\Livewire\News\Index as NewsIndex;
use App\Livewire\News\CreateNews;
use App\Livewire\News\ShowNews;
use App\Livewire\Events\Index as EventIndex;
use App\Livewire\Events\ShowEvent;
use App\Livewire\Documents\Index as DocumentIndex;
use App\Livewire\Documents\CreateDocument;
use App\Livewire\Documents\ShowDocument;
use App\Livewire\Profile\ShowProfile;
use App\Livewire\Profile\EditProfile;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
});

// Authenticated routes with Jetstream middleware
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // Homepage (Dashboard)
    Route::get('/', Homepage::class)->name('home');
    
    // Memo routes
    Route::prefix('memos')->name('memos.')->group(function () {
        Route::get('/', MemoIndex::class)->name('index');
        Route::get('/create', CreateMemo::class)->name('create');
        Route::get('/pending', PendingAcknowledgment::class)->name('pending');
        Route::get('/{memo}', ShowMemo::class)->name('show');
        Route::get('/{memo}/edit', EditMemo::class)->name('edit');
    });
    
    // News routes
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', NewsIndex::class)->name('index');
        Route::get('/create', CreateNews::class)->name('create');
        Route::get('/{news}', ShowNews::class)->name('show');
    });
    
    // Events routes
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', EventIndex::class)->name('index');
        Route::get('/{event}', ShowEvent::class)->name('show');
    });
    
    // Documents routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', DocumentIndex::class)->name('index');
        Route::get('/create', CreateDocument::class)->name('create');
        Route::get('/{document}', ShowDocument::class)->name('show');
    });
    
    // Profile routes
    //Route::prefix('profile')->name('profile.')->group(function () {
      //  Route::get('/', ShowProfile::class)->name('show');
        //Route::get('/edit', EditProfile::class)->name('edit');
    //});
    
    // Reports redirect route
    Route::get('/reports', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.reports');
        } elseif ($user->isHOD()) {
            return redirect()->route('hod.reports');
        }
        abort(403, 'You do not have permission to view reports.');
    })->name('reports.index');
    
    // Staff directory redirect route
    Route::get('/staff', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.staff.index');
        }
        abort(403, 'You do not have permission to view staff directory.');
    })->name('staff.index');
    
    // Admin only routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/staff', \App\Livewire\Admin\StaffIndex::class)->name('staff.index');
        Route::get('/staff/create', \App\Livewire\Admin\CreateStaff::class)->name('staff.create');
        Route::get('/staff/{user}', \App\Livewire\Admin\EditStaff::class)->name('staff.edit');
        //Route::get('/staff/{user}/show', \App\Livewire\Admin\StaffShow::class)->name('staff.show');
        Route::get('/reports', \App\Livewire\Admin\Reports::class)->name('reports');
        Route::get('/departments', \App\Livewire\Admin\Departments::class)->name('departments');
        Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings');
    });
    
    // HOD only routes
    Route::prefix('hod')->name('hod.')->group(function () {
        Route::get('/department-staff', \App\Livewire\Hod\DepartmentStaff::class)->name('staff');
        Route::get('/department-staff/{user}', \App\Livewire\Hod\StaffShow::class)->name('staff.show');
        Route::get('/dept-reports', \App\Livewire\Hod\DepartmentReports::class)->name('reports');
    });
    
    // Logout - Jetstream handles this, but keep for reference
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});