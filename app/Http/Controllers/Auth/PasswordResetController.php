<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;


class PasswordResetController extends Controller
{
  public function showLinkRequestForm()
  {
    return view('authentication.password-recovery.forgot-password');
  }

 /// Send Reset Link Email
  public function sendResetLinkEmail(Request $request)
  {
    try {
      $validator = \Validator::make($request->all(), [
        'forgot_password_email' => 'required|email'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors(),
          'message' => 'Validation failed.',
        ], 422);
      }

      $status = Password::sendResetLink([
        'email' => $request->input('forgot_password_email')
      ]);

      if ($status === Password::RESET_LINK_SENT) {
        session()->flash('flash_message', __($status));
        return response()->json([
          'success' => true,
          'redirect_url' => '/',
        ]);
      }

      return response()->json([
        'success' => false,
        'errors' => ['forgot_password_email' => [__($status)]],
        'message' => 'Password reset failed.',
      ], 422);
    } catch (\Exception $e) {
      \Log::error("Password reset link error: {$e->getMessage()} | File: {$e->getFile()} | Line: {$e->getLine()}");
      return response()->json([
        'success' => false,
        'message' => 'Something went wrong sending the reset link. Please try again later.',
      ], 500);
    }
  }

  // Show Reset Form
  public function showResetForm($token)
  {
    try {
      return view('authentication.password-recovery.reset-password', 
        ['token' => $token]);
    } catch (\Exception $e) {
      \Log::error("Show reset form error: {$e->getMessage()}");
      return redirect()->route('/')->with('error', 'Invalid reset token.');
    }
  }

  // Reset Password (returns HTML responses)
  public function reset(Request $request)
  {
    try {
      $validator = \Validator::make($request->all(), [
        'token' => 'required',
        'reset_email' => 'required|email',
        'reset_password' => [
          'required',
          'confirmed',
          PasswordRule::min(8)
            ->mixedCase()
            ->letters()
            ->numbers()
            ->symbols(),
        ],
      ]);

      if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
      }

      $resetData = [
        'email' => $request->input('reset_email'),
        'password' => $request->input('reset_password'),
        'password_confirmation' => $request->input('reset_password_confirmation'),
        'token' => $request->input('token'),
      ];

      $status = Password::reset($resetData, function (User $user, string $password) {
          $user->forceFill([
            'password' => Hash::make($password)
          ])->setRememberToken(Str::random(60));
          $user->save();
          event(new PasswordReset($user));
        }
      );

      if ($status === Password::PASSWORD_RESET) {
        session()->flash('flash_message', __($status));
        return redirect('/');
      }

      return back()->withErrors([
        'reset_email' => __($status),
      ])->withInput();

    } catch (\Exception $e) {
      \Log::error("Password reset error: {$e->getMessage()} | File: {$e->getFile()} | Line: {$e->getLine()}");
      return back()->withErrors([
        'reset_email' => 'Something went wrong resetting your password. Please try again later.',
      ]);
    }
  }
}