<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
	public function showVerificationNotice()
	{
		return view('authentication.verification.verify-email');
	}

	public function sendVerificationNotification(Request $request)
	{
		$request->user()->sendEmailVerificationNotification();
		return back()->with('message', 'Verification link sent!');
	}

	public function verify(EmailVerificationRequest $request)
	{
		$request->fulfill();
		return redirect('/');
	}
}

