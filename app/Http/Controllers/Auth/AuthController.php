<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use App\Traits\ApiValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Log};
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
  use ApiValidator;

  protected AuthService $authService;

  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }

  public function storeSignup(Request $request): JsonResponse
  {
    $validated = $this->validateJson($request, [
    'username' => ['required', 'string', 'min:3', 'max:30', 'unique:users,username'],
    'signup_email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
    'signup_password' => ['required', 'string', Password::min(8)
                      ->max(64)
                      ->letters()
                      ->numbers()
                      ->symbols()
                      ->uncompromised()],
    ]);

    $user = User::create([
      'username' => $validated['username'],
      'email' => $validated['signup_email'],
      'password' => Hash::make($validated['signup_password']),
    ]);

    event(new Registered($user));
    Auth::login($user);

    session()->flash('flash_message', 'Signup successful!');
    return response()->json(['success' => true]);
  }

  public function checkSignin(Request $request): JsonResponse
  {
    $email = (string) $request->input('signin_email');
    $throttleKey = strtolower($email) . '|' . $request->ip();

    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
      return response()->json([
        'errors' => ['signin_email' => ['Too many attempts. Try again in ' . RateLimiter::availableIn($throttleKey) . 's.']]
      ], 429);
    }

    $validated = $this->validateJson($request, [
      'signin_email' => 'required|email',
      'signin_password' => 'required',
    ]);

    if (!Auth::attempt(['email' => $validated['signin_email'], 'password' => $validated['signin_password']])) {
      RateLimiter::hit($throttleKey);
      return response()->json(['errors' => ['signin_email' => ['Invalid credentials.']]], 422);
    }

    RateLimiter::clear($throttleKey);
    $request->session()->regenerate();

    $user = Auth::user();
    Log::info("User #{$user->id} signed in successfully", ['ip' => $request->ip()]);

    session()->flash('flash_message', 'Welcome back, ' . Auth::user()->username);
    return response()->json(['success' => true]);
  }

  public function logOut(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
  }
}