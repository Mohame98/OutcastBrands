<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
  use ApiValidator;

  public function send(Request $request, User $user): JsonResponse
  {
    $this->authorizeJson(Auth::check());
    $this->authorizeJson(Auth::id() !== $user->id);

    $sender = Auth::user(); 
    $receiver = $user; 

    $validated = $this->validateJson($request, [
      'subject' => 'nullable|string|max:255|regex:/^[\p{L}\p{N}\p{P}\p{Zs}]+$/u',
      'message' => 'required|string|max:1000|regex:/^[\p{L}\p{N}\p{P}\p{Zs}\r\n]+$/u',
    ]);

    $validated['subject'] = $validated['subject'] ?? null;
    $validated['message'] = strip_tags($validated['message']);

    try {
      Mail::send('emails.contact', [
        'data' => [
          'sender' => $sender,
          'receiver' => $receiver,
          'message_body' => $validated['message'],
        ]
      ], function ($message) use ($sender, $receiver, $validated) {
        $message->to($receiver->email)
          ->subject($validated['subject'] ?? 'New message from ' . $sender->username)
          ->replyTo($sender->email, $sender->username)
          ->from(config('mail.from.address'), $sender->username);
      });

      return response()->json([
        'success' => true,
        'message' => 'Your message was sent to ' . $receiver->username,
      ]);
    } catch (\Exception $e) {
      Log::error('Mail sending failed: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Could not send email at this time. Please try again later.'
      ], 500);
    }
  }
}