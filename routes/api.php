<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\LoginRegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\SocialMediaController;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\CommanController;
use App\Http\Controllers\Api\User\TwilioController;
use App\Http\Controllers\Api\User\PaymentController;
use App\Http\Controllers\Api\User\StripeWebhookController;
use App\Http\Controllers\Api\User\SeminarController;
use App\Http\Controllers\Api\User\WebinarController;
use App\Http\Controllers\Api\User\PostController;
use App\Http\Controllers\Api\User\PageController;
use App\Http\Controllers\Api\User\ContactController;
use App\Http\Controllers\Api\User\EducationCategoryController;
use App\Http\Controllers\Api\User\HeroController;
use App\Http\Controllers\Api\User\EventRequestController;
use App\Http\Controllers\Api\User\EducationController;
use App\Http\Controllers\Api\User\VolunteerController;
use App\Http\Controllers\Api\User\HabitController;
use App\Http\Controllers\Api\User\FeedController;
use App\Http\Controllers\Api\User\LeaderboardController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(LoginRegisterController::class)->group(function () {

    Route::post('register', 'register');

    Route::post('login', 'login');

    Route::post('forgot-password', 'forgotPassword');

    Route::post('reset-password', 'resetPassword');

    Route::get('/email/verify/{id}/{hash}', 'verifyEmail');

    Route::post('verify-set-password', 'verifyBuyerEmailAndSetPassword');

    Route::get('get-email/{id}', 'getEmail');
});

Route::controller(SocialMediaController::class)->group(function () {

    Route::post('handle-google', 'handleGoogle');

    Route::post('handle-facebook', 'handleFacebook');
});


Route::group(['middleware' => ['api', 'auth:sanctum']], function () {

    Route::post('logout', [LogoutController::class, 'logout']);

    Route::get('user-details', [ProfileController::class, 'userDetails']);

    Route::post('update-profile', [ProfileController::class, 'updateProfile']);

    Route::post('update-profile-image', [ProfileController::class, 'changeProfileImage']);

    Route::post('update-user-status', [ProfileController::class, 'updateStatus']);

    Route::post('update-password', [ProfileController::class, 'updatePassword']);


    // Route::post('send-sms', [TwilioController::class, 'sendSms']);

    Route::get('/get-all-webinars', [WebinarController::class, 'index']);

    Route::post('/completed-task', [HomeController::class, 'store']);

    Route::get('/dashboard', [HomeController::class, 'index']);

    Route::get('/notifications', [HomeController::class, 'userNotifications']);
    Route::delete('/notification/{id}', [HomeController::class, 'deleteNotification']);

    Route::get('/reward', [HomeController::class, 'getReward']);    

    Route::get('/event-requests', [EventRequestController::class, 'index']);

    Route::post('/update-event-request', [EventRequestController::class, 'updateStatus']);

    Route::get('/event-history', [EventRequestController::class, 'eventHistory']);

    Route::post('/volunteer-availability', [VolunteerController::class, 'volunteerAvailability']);

    Route::post('/add-availability', [VolunteerController::class, 'storeVolunteerAvailability']);

    Route::get('/delete-availability/{id}', [VolunteerController::class, 'destroyVolunteerAvailability']);

    Route::post('/event-details', [VolunteerController::class, 'eventDetails']);

    // Gamification & Core App Features
    Route::get('/habits', [HabitController::class, 'index']);
    Route::post('/habits', [HabitController::class, 'store']);
    Route::post('/habits/{id}/check', [HabitController::class, 'check']);
    
    Route::get('/feeds', [FeedController::class, 'index']);
    
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
});

use App\Http\Controllers\Api\User\ChatbotController;

Route::post('/ask-coach', [ChatbotController::class, 'askCoach']);

Route::get('/get-locations', [CommanController::class, 'getLocations']);

Route::get('/get-site-settings-details', [CommanController::class, 'siteSettingDetails']);
Route::get('/get-latest-records/{pageName}', [CommanController::class, 'getLatestRecords']);


Route::get('/get-all-seminars', [SeminarController::class, 'index']);

Route::get('/get-posts/{postType}', [PostController::class, 'index']);
Route::get('/post/{slug}', [PostController::class, 'show']);

Route::get('/page/{slug}', [PageController::class, 'getPageDetails']);

Route::post('/contact-us', [ContactController::class, 'store']);

Route::get('/heroes/{type?}', [HeroController::class, 'index']);

Route::get('/hero/{slug}', [HeroController::class, 'show']);

Route::get('/education', [EducationController::class, 'index']);

Route::get('/education/{slug}', [EducationController::class, 'show']);

Route::get('/education-categories', [EducationCategoryController::class, 'index']);
Route::get('/education-category/{slug}', [EducationCategoryController::class, 'show']);

//Payments Routes
Route::post('/checkout-session', [PaymentController::class, 'createCheckoutSession']);

Route::post('/checkout-success', [PaymentController::class, 'checkoutSuccess']);

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleStripeWebhook']);
