<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;

class SessionController extends Controller
{
  // Signup
  public function storeSignup(Request $request)
  {
    try {
      $validator = \Validator::make($request->all(), [
        'username' => 'required|string|max:90',
        'signup_email' => 'required|string|email|max:255|unique:users,email',
        'signup_password' => [
          'required',
          Password::min(8)
            ->mixedCase()
            ->letters()
            ->numbers()
            ->symbols(),
        ],
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors(),
          'message' => 'Validation failed.',
        ], 422);
      }

      $validated = $validator->validated();

      $user = User::create([
        'username' => $validated['username'],
        'email' => $validated['signup_email'],
        'password' => Hash::make($validated['signup_password']),
      ]);

      event(new Registered($user));
      Auth::login($user);

      session()->flash('flash_message', 'Signup successful. Please verify your account.');
      return response()->json([
        'success' => true,
      ]);
    } catch (ValidationException $e) {
      throw $e;
    } catch (\Exception $e) {
      \Log::error('Signup error: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Something went wrong during signup. Please try again later.',
      ], 500);
    }
  }

  // signin
  public function checkSignin(Request $request)
  {
    try {
      $email = (string) $request->input('signin_email');
      $throttleKey = strtolower($email) . '|' . $request->ip();

      if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        throw ValidationException::withMessages([
          'signin_email' => __('Too many login attempts. Please try again in :seconds seconds.', [
            'seconds' => RateLimiter::availableIn($throttleKey),
          ]),
        ]);
      }

      $attributes = $request->validate([
        'signin_email' => ['required', 'email'],
        'signin_password' => ['required'],
      ]);

      if (!Auth::attempt([
        'email' => $attributes['signin_email'],
        'password' => $attributes['signin_password'],
      ])) {
        RateLimiter::hit($throttleKey);
        throw ValidationException::withMessages([
          'signin_email' => 'Invalid email or password.',
        ]);
      }

      RateLimiter::clear($throttleKey);
      $request->session()->regenerate();

      session()->flash('flash_message', 'Hello, welcome ' . e(auth()->user()->username) . '!');
      return response()->json([
        'success' => true,
      ]);
    } catch (ValidationException $e) {
      throw $e;
    } catch (\Exception $e) {
      \Log::error('Signin error: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Something went wrong. Please try again later.',
      ], 500);
    }
  }

  public function edit()
  {
    return view("authentication.pages.edit");
  }

   public function profile()
  {
    return view("authentication.pages.profile");
  }

  // edit account
  public function changeProfileImage(Request $request)
  {
    $user = Auth::user();
    $validatedData = $request->validate([
      'profile_image' => 'nullable|image|mimes:png,jpg|max:1000',
    ]);

    if ($request->hasFile('profile_image')) {
      // delete old image
      if ($user->profile_image) {
        Storage::disk('public')->delete($user->profile_image);
      }

      $imagePath = $request->file('profile_image')->store('profile_images', 'public');
      $user->profile_image = $imagePath;
    } else {
      if ($user->profile_image) {
        Storage::disk('public')->delete($user->profile_image);
      }
      $user->profile_image = null;
      $user->save();
      return response()->json([
        'success' => true,
        'message' => 'Profile image removed.',
        'profile_image_url' => null,
        'email_initial' => strtoupper(substr($user->email, 0, 1)),
      ]);
    }

    $user->save();
    return response()->json([
      'success' => true,
      'message' => 'Profile image has been updated.',
      'profile_image_url' => asset('storage/' . $user->profile_image),
    ]);
  }

  public function changeUsername(Request $request)
  {
    $user = Auth::user();
    $validatedData = $request->validate([
      'username' => [
        'required',
        'string',
        'max:90',
        'regex:/^[a-zA-Z0-9._-]+$/',
        'unique:users,username,' . $user->id,
      ],
    ]);

    if (isset($validatedData['username'])) {
      $user->username = $validatedData['username'];
    }

    $user->save();
    if ($request->expectsJson()) {
      return response()->json([
        'success' => true,
        'message' => 'Username has been updated.',
        'username' => $user->username,
      ]);
    }
    return redirect('account/edit')->with('success', 'Username has been updated.');
  }

  public function changeBio(Request $request)
  {
    $user = Auth::user();
    $validatedData = $request->validate([
      'bio' => [
        'required',
        'string',
        'max:200',
      ],
    ]);

    if (isset($validatedData['bio'])) {
      $user->bio = $validatedData['bio'];
    }

    $user->save();
    if ($request->expectsJson()) {
      return response()->json([
        'success' => true,
        'message' => 'Bio has been updated.',
        'bio' => $user->bio,
      ]);
    }
    return redirect('account/edit')->with('success', 'Bio has been updated.');
  }

  public function changeLocation(Request $request)
  {
    $user = Auth::user();
    $validatedData = $request->validate([
      'user_location' => [
        'required',
        'string',
        'max:90',
      ],
    ]);

    if (isset($validatedData['user_location'])) {
      $user->user_location = $validatedData['user_location'];
    }

    $user->save();
    if ($request->expectsJson()) {
      return response()->json([
        'success' => true,
        'message' => 'Location has been updated.',
        'user_location' => $user->user_location,
      ]);
    }
    return redirect('account/edit')->with('success', 'user_location has been updated.');
  }

  public function changeInstagram(Request $request)
  {
    $user = Auth::user();

    if ($request->filled('instagram') && !Str::startsWith($request->input('instagram'), ['http://', 'https://'])) {
      $request->merge([
        'instagram' => 'https://' . ltrim($request->input('instagram'), '/'),
      ]);
    }

    $validatedData = $request->validate([
      'instagram' => [
        'required',
        'url',
      ],
    ]);

    if (isset($validatedData['instagram'])) {
      $user->instagram = $validatedData['instagram'];
    }

    $user->save();
    if ($request->expectsJson()) {
      return response()->json([
        'success' => true,
        'message' => 'instagram has been updated.',
        'instagram' => $user->instagram,
      ]);
    }
    return redirect('account/edit')->with('success', 'instagram has been updated.');
  }

  public function changePassword(Request $request)
  {
    $user = Auth::user();
    $validatedData = $request->validate([
      'current_password' => ['required', 'string'],
      'password' => [
      'required',
      'confirmed',
      Password::min(8)
        ->mixedCase()
        ->letters()
        ->numbers()
        ->symbols(),
      ],
    ]);

    if (!Hash::check($validatedData['current_password'], $user->password)) {
      return response()->json([
        'success' => false,
        'message' => [
          'current_password' => 'The current password is incorrect.'
        ]
      ], 422);
    }

    if (!empty($validatedData['password'])) {
      $user->password = Hash::make($validatedData['password']);
      $user->save();
    }

    return response()->json([
      'success' => true,
      'message' => 'Password updated.',
    ]);
  }

  // delete account
  public function deleteAccount(Request $request)
  {
    $request->validate([
      'confirm_deletion' => ['required', 'current_password'],
    ]);

    $user = Auth::user();
    $user->delete();

    Auth::logout();
    return response()->json([
      'success' => true,
      'message' => 'Your account has been deleted.',
    ]);
  }

  // logout
  public function logOut(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    session()->flash('flash_message', 'Logged Out');
    return redirect('/');
  }
}