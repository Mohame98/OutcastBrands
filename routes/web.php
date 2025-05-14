<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\SessionController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

Route::get('/', function () {
  return view('pages/home');
});

Route::get('/profile', function () {
  return view('pages/profile');
})->middleware(['auth', 'verified']);

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
  Route::get('/signup', 
  [SessionController::class, 'signupView'])
    ->name('signup');

  Route::post('/signup', 
  [SessionController::class, 'storeSignup']);

  Route::get('/signin', 
  [SessionController::class, 'signinView'])
    ->name('signin');

  Route::post('/signin', 
  [SessionController::class, 'checkSignin']);
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
  [SessionController::class, 'logOut'])
    ->name('logout');
});

Route::middleware(['auth', 'verified'])->group(function () {
  // edit account
  Route::get('/account/edit', 
  [SessionController::class, 'edit']);

  Route::patch('/account/change-profile-image', 
  [SessionController::class, 'changeProfileImage'])
    ->name('profileImg.change');
    
  Route::patch('/account/change-username', 
  [SessionController::class, 'changeUsername'])
    ->name('username.change');

  Route::patch('/account/change-password', 
  [SessionController::class, 'changePassword'])
    ->name('password.change');

  // delete account
  Route::get('/account/delete-confirmation', 
  [SessionController::class, 'deleteAccountConfirmation']);       

  Route::delete('/account/delete', 
  [SessionController::class, 'deleteAccount'])
    ->name('account.delete');
});