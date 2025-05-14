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
use App\Models\User;

class SessionController extends Controller
{
  // signup
  public function signupView()
  {
    return view("authentication.pages.signup");
  }

  public function storeSignup(Request $request)
  {
    $validated = $request->validate([
      'username' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|confirmed|min:2',
    ]);

    $user = User::create([
      'username' => $validated['username'],
      'email' => $validated['email'],
      'password' => Hash::make($validated['password']),
    ]);

    event(new Registered($user));
    Auth::login($user);
    return redirect('/signin');
  }

  // signin
  public function signinView()
  {
    return view("authentication.pages.signin");
  }

  public function checkSignin(Request $request)
  {
    $attributes = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
    ]);

    if (!Auth::attempt($attributes)) {
      throw ValidationException::withMessages([
        'email' => 'Invalid email or password',
      ]);
    }
    $request->session()->regenerate();
    return redirect('/');
  }

  public function edit()
  {
    return view("authentication.pages.edit");
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
      return response()->json([
        'success' => true,
        'message' => 'Profile image removed.',
        'profile_image_url' => null,
      ]);
    }

    $user->save();
    return response()->json([
      'success' => true,
      'message' => 'Profile image changed successfully.',
      'profile_image_url' => asset('storage/' . $user->profile_image),
    ]);
  }

  public function changeUsername(Request $request)
  {
    $user = Auth::user();
    $validatedData = $request->validate([
      'username' => 'nullable|string|max:255',
    ]);

    if (isset($validatedData['username'])) {
      $user->username = $validatedData['username'];
    }

    $user->save();
    return redirect('account/edit')->with('success', 'username changed successfully.');
  }

  public function changePassword(Request $request)
  {
    $user = Auth::user();
    $validatedData = $request->validate([
      'current_password' => 'required|string',
      'password' => 'required|string|confirmed|',
    ]);

    if (!Hash::check($validatedData['current_password'], $user->password)) {
      return response()->json([
        'status' => 'error',
        'message' => 'The current password is incorrect.',
      ], 422);
    }

    if (!empty($validatedData['password'])) {
      $user->password = Hash::make($validatedData['password']);
      $user->save();
    }

    return response()->json([
      'status' => 'success',
      'message' => 'Password updated successfully.',
    ]);
  }

  // delete account
  public function deleteAccountConfirmation()
  {
    return view('authentication.delete-confirmation');
  }

  public function deleteAccount(Request $request)
  {
    $user = Auth::user();
    $user->delete();
    return redirect('/');
  }

  // logout
  public function logOut(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/signin');
  }
}

// if (!Auth::check()) {
//   return redirect()->route('login');
// }
