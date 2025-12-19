<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\PhoneVerification;
use App\Livewire\StudioList;
use App\Livewire\StudioBooking;
use App\Livewire\MyBookings;

// Home page - redirect to studios if authenticated, otherwise show verification
// Home page - Studio Listing (Public)
Route::get('/', StudioList::class)->name('home');
Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::get('/studios', StudioList::class)->name('studios.index');

// Studio Details (Public)
Route::get('/studios/{studio}', \App\Livewire\StudioDetails::class)->name('studios.show');

// Authentication
Route::get('/login', \App\Livewire\Auth\Login::class)->name('login')->middleware('guest');
Route::get('/register', \App\Livewire\Auth\Register::class)->name('register')->middleware('guest');
Route::get('/verify-phone', function() {
    return redirect()->route('login');
});

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Booking a studio
    Route::get('/studios/{studio}/book', StudioBooking::class)->name('studios.book');
    
    // User's bookings
    Route::get('/my-bookings', MyBookings::class)->name('my-bookings');
    
    // Logout
    Route::match(['get', 'post'], '/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
    
    // Invoice routes
    Route::get('/invoices/{invoice}/print', [\App\Http\Controllers\InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\InvoiceController::class, 'downloadPdf'])->name('invoices.download');

    // User Profile
    Route::get('/profile', \App\Livewire\UserProfile::class)->name('profile');
});

// Dynamic Pages
Route::get('/pages/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
    return view('page', ['page' => $page]);
})->name('pages.show');
