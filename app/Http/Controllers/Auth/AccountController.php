<?php

namespace App\Http\Controllers\Auth;

use App\Models\User; 
use App\Traits\ApiValidator;
use App\Services\{ProfileService, AccountService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Log};
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
  use ApiValidator;

  protected ProfileService $profileService;
  protected AccountService $accountService;

  public function __construct(ProfileService $profileService, AccountService $accountService)
  {
    $this->profileService = $profileService;
    $this->accountService = $accountService;
  }

  public function edit()
  {
    return view("authentication.pages.edit");
  }

  public function changeUsername(Request $request): JsonResponse
  {
    $user = Auth::user();
    $newUsername = $request->input('username');

    if ($user->username === $newUsername) {
      return response()->json([
        'success' => false, 
        'message' => 'No changes made.', 
        'username' => $newUsername,
      ], 200);
    }

    $validated = $this->validateJson($request, [
      'username' => 'required|string|max:90|regex:/^[a-zA-Z0-9._-]+$/|unique:users,username,' . $user->id,
    ]);

    $oldUsername = $user->username;
    $this->profileService->updateField($user, 'username', $validated['username']);

    Log::info("User #{$user->id} changed username", [
      'old' => $oldUsername,
      'new' => $user->username,
      'ip'  => $request->ip()
    ]);

    return response()->json([
      'success' => true, 
      'username' => $user->username,
      'message' => 'Username Updated.',
    ]);
  }

   /**
   * Change Password
   */
  public function changePassword(Request $request): JsonResponse
  {
    $user = Auth::user();

    $validated = $this->validateJson($request, [
      'current_password' => ['required', 'string'],
      'password' => [
        'required',
        'confirmed',
        Password::min(8)->mixedCase()->letters()->numbers()->symbols(),
      ],
    ]);

    if (Hash::check($validated['password'], $user->password)) {
      return response()->json([
        'success' => false,
        'errors' => ['password' => ['New password cannot be the same as current.']],
      ], 422);
    }

    // Verify current password
    if (!Hash::check($validated['current_password'], $user->password)) {
      return response()->json([
        'success' => false,
        'errors' => ['current_password' => ['Incorrect current password.']],
      ], 422);
    }

    $this->accountService->updatePassword($user, $validated['password']);
    Log::info("User #{$user->id} changed password");
    return response()->json(['success' => true, 'message' => 'Password updated.']);
  }

  public function deleteAccount(Request $request): JsonResponse
  {
    $user = Auth::user();
    
    $this->validateJson($request, [
      'confirm_deletion' => 'required|current_password',
    ]);

    $this->accountService->deleteUserAssets($user);
    $user->delete();
    Auth::logout();

    return response()->json(['success' => true]);
  }
}