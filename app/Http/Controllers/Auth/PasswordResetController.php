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


class PasswordResetController extends Controller
{
  public function showLinkRequestForm()
  {
    return view('authentication.password-recovery.forgot-password');
  }

  public function sendResetLinkEmail(Request $request)
  {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink(
      $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {
      session()->flash('flash_message', __($status));
      return response()->json([
        'success' => true,
        'redirect_url' => '/',
      ]);
    }

    return back()->withErrors([
      'email' => __($status),
    ]);
  }

  public function showResetForm($token)
  {
    return view('authentication.password-recovery.reset-password', 
    ['token' => $token]);
  }

  public function reset(Request $request)
  {
    $request->validate([
      'token' => 'required',
      'email' => 'required|email',
      'password' => [
      'required',
      PasswordRule::min(8)
        ->mixedCase()
        ->letters()
        ->numbers()
        ->symbols(),
      ],
    ]);

    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function (User $user, string $password) {
        $user->forceFill([
          'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));
      }
    );

    if ($status === Password::PASSWORD_RESET) {
      session()->flash('flash_message', __($status));
      return redirect()->route('/');
    }

    return back()->withErrors([
      'email' => __($status),
    ]);
  }
}