<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\{AuthController, ProfileController, AccountController};
use App\Http\Controllers\Brands\BrandsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

Route::get('/', 
[BrandsController::class, 'index'])
  ->name('home');

Route::get('api/brands/search', 
[SearchController::class, 'searchApi']);

Route::get('/search', 
[SearchController::class, 'searchView'])
  ->name('search');

Route::get('/brands/{brand}', 
[BrandsController::class, 'showBrand'])
  ->name('brand.show');

Route::get('api/brands/{brand}/comments', 
[CommentController::class, 'getCommentsApi']);

Route::get('/api/profile/{user}/brands', 
[UserProfileController::class, 'brandProfileApi']);

Route::get('/profile/{user}', 
[UserProfileController::class, 'userProfile'])
  ->name('profile.show');

Route::post('/brands/{brand}/save', 
[BrandsController::class, 'toggleSave'])
  ->name('brands.save');

Route::post('/brands/{brand}/vote',
[BrandsController::class, 'vote'])
  ->name('brands.vote');

Route::post('/comments/{comment}/like', 
[CommentController::class, 'likeComments'])
  ->name('comments.like');

Route::post('/brands/{brand}/comments', 
[CommentController::class, 'addComment'])
  ->name('comment.add');

Route::put('/comments/{comment}/edit', 
[CommentController::class, 'editComment'])
->name('comments.edit');

Route::delete('/comments/{comment}/delete', 
[CommentController::class, 'deleteComment'])
  ->name('comments.delete');

Route::middleware('guest')->group(function () {
  // Password Reset Routes
  Route::get('/forgot-password', 
  [PasswordResetController::class, 'showLinkRequestForm'])
    ->name('password.request');

  Route::post('/forgot-password', 
  [PasswordResetController::class, 'sendResetLinkEmail'])
    ->name('password.email');

  Route::get('/reset-password/{token}', 
  [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');

  Route::post('/reset-password', 
  [PasswordResetController::class, 'reset'])
    ->name('password.update');

  // login routes
  Route::post('/signup', 
  [AuthController::class, 'storeSignup'])
    ->name('signup');

  Route::post('/signin', 
  [AuthController::class, 'checkSignin'])
    ->name('login');
});

Route::middleware('auth')->group(function () {
  // Email Verification Routes
  Route::get('/email/verify', 
  [EmailVerificationController::class, 'showVerificationNotice'])
    ->name('verification.notice');

  Route::post('/email/verification-notification', 
  [EmailVerificationController::class, 'sendVerificationNotification'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

  Route::get('/email/verify/{id}/{hash}', 
  [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

  // logout
  Route::post('/logout', 
  [AuthController::class, 'logOut'])
    ->name('logout');
});

Route::middleware(['auth', 'verified'])->group(function () {
  // edit account
  Route::get('/account/edit', 
  [AccountController::class, 'edit'])
    ->name('account.edit');

  Route::patch('/account/change-username', 
  [AccountController::class, 'changeUsername'])
    ->name('username.change');

  Route::patch('/account/change-password', 
  [AccountController::class, 'changePassword'])
    ->name('password.change');

  // edit profile
  Route::get('/account/profile', 
  [ProfileController::class, 'profile'])
    ->name('account.profile'); 

  Route::patch('/account/change-profile-image', 
  [ProfileController::class, 'changeProfileImage'])
    ->name('profileImg.change');

  Route::patch('/account/change-bio', 
  [ProfileController::class, 'changeBio'])
    ->name('bio.change');

  Route::patch('/account/change-instagram', 
  [ProfileController::class, 'changeInstagram'])
    ->name('instagram.change');

  Route::patch('/account/change-location', 
  [ProfileController::class, 'changeLocation'])
    ->name('location.change');

  // delete account
  Route::get('/account/delete-confirmation', 
  [AccountController::class, 'deleteAccountConfirmation']);       

  Route::delete('/account/delete', 
  [AccountController::class, 'deleteAccount'])
    ->name('account.delete');

  Route::post('/add-brands/step-1', 
  [BrandsController::class, 'storeBrand1'])
    ->name('brands.store.step1');

  Route::post('/add-brands/step-2', 
  [BrandsController::class, 'storeBrand2'])
    ->name('brands.store.step2');
    
  Route::post('/add-brands/step-3', 
  [BrandsController::class, 'storeBrand3'])
    ->name('brands.store.step3');

  Route::post('/add-brands/step-4', 
  [BrandsController::class, 'storeBrand4'])
    ->name('brands.store.step4');

  Route::post('/report/step-1', 
  [ReportController::class, 'storeReportStep1']);

  Route::post('/report/step-2', 
  [ReportController::class, 'storeReportStep2']);

  Route::get('/saved-brands/profile', 
  [UserProfileController::class, 'savedBrands'])
    ->name('profile.saved-brands');

  Route::delete('/brands/{brand}', 
  [BrandsController::class, 'deleteBrand'])
    ->name('brand.delete');

  Route::post('api/brands/{brand}/comments', 
  [CommentController::class, 'addComment']);

  Route::get('api/saved-brands/profile', 
  [UserProfileController::class, 'savedBrandsApi']);

  Route::post('/profile/{user}', 
  [ContactController::class, 'send'])
    ->name('contact.send');
});