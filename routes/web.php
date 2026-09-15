<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return redirect()->route('auth.login');
});

// list storage
Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return '<h1>Storage linked</h1>';
});

//Clear Cache facade value:
Route::get('/cache-clear', function () {
    Artisan::call('optimize:clear');
    return '<h1>All Cache cleared</h1>';
});

// Auth::routes(['verify' => true]);

Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');

Route::group(['middleware' => ['web', 'guest'], 'as' => 'auth.', 'prefix' => ''], function () {
    // Route::view('signup', 'auth.admin.register')->name('register');
    Route::view('/login', 'auth.admin.login')->name('login');
    Route::view('forget-password', 'auth.admin.forget-password')->name('forget-password');
    Route::view('reset-password/{token}/{email}', 'auth.admin.reset-password')->name('reset-password');
});

Route::group(['middleware' => ['auth', 'preventBackHistory']], function () {
    Route::view('/profile', 'auth.profile.index')->name('auth.admin-profile');
    Route::group(['as' => 'admin.', 'prefix' => ''], function () {
        Route::view('dashboard', 'admin.index')->name('dashboard');

        Route::view('webinars', 'admin.webinar.index')->name('webinars');

        Route::view('blogs', 'admin.blog.index')->name('blogs');

        Route::view('news', 'admin.news.index')->name('news');

        Route::view('settings', 'admin.setting.index')->name('settings');

        Route::view('transactions', 'admin.transactions.index')->name('transactions');

        Route::view('quotes', 'admin.quote.index')->name('quotes');

        Route::view('seminars', 'admin.seminar.index')->name('seminars');

        Route::view('health', 'admin.health.index')->name('health');

        Route::view('contacts', 'admin.contact.index')->name('contacts');

        Route::view('users', 'admin.user.index')->name('users');

        Route::view('volunteers', 'admin.volunteer.index')->name('volunteers');
        Route::view('categories', 'admin.category.index')->name('categories');
        Route::view('education', 'admin.education.index')->name('education');
        Route::view('events', 'admin.event.index')->name('events');

        // Route::view('heroes', 'admin.heroe.index')->name('heroes');

        Route::view('page-manage', 'admin.page-manage.index')->name('page-manage');

        Route::view('page-manage/{slug}/sections', 'admin.page-manage.section')->name('page-sections');


        Route::view('misreport', 'admin.mis-report.index')->name('mis-report');

        Route::view('locations', 'admin.location.index')->name('locations');


    });
});
