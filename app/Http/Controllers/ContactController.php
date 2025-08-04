<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;

class ContactController extends Controller
{
  public function send(Request $request, User $user)
  {
    $sender = Auth::user(); 
    $receiver = $user; 

    $validated = $request->validate([
      'subject' => 'nullable|string|max:255',
      'message' => 'required|string',
    ]);

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
        ->from(config('services.email.address'), $sender->username);
    });

    return response()->json([
      'success' => true,
      'message' => 'Your message was sent to ' . $receiver->username,
    ]);
  }
}
