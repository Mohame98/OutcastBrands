<?php

namespace App\Http\Controllers\Auth;

use App\Models\User; 
use App\Traits\ApiValidator;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Log};
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProfileController extends Controller
{
  use ApiValidator;

  protected ProfileService $profileService;

  public function __construct(ProfileService $profileService)
  {
    $this->profileService = $profileService;
  }

  public function profile()
  {
    return view("authentication.pages.profile");
  }

  public function changeProfileImage(Request $request): JsonResponse
  {
    $user = Auth::user();
    
    $this->validateJson($request, [
      'profile_image' => 'nullable|image|mimes:png,jpg,jpeg|max:1000',
    ]);

    $oldPath = $user->profile_image;

    if ($request->hasFile('profile_image')) {
      $newPath = $this->profileService->updateProfileImage($user, $request->file('profile_image'));

      Log::info("User #{$user->id} uploaded new profile image", [
        'old_path' => $oldPath,
        'new_path' => $newPath,
        'ip'       => $request->ip()
      ]);
      
      return response()->json([
        'message' => 'Profile image updated.',
        'success' => true,
        'profile_image_url' => asset('storage/' . $newPath)
      ]);
    }

    if ($oldPath) {
      $this->profileService->updateProfileImage($user, null);
      
      Log::info("User #{$user->id} removed profile image", [
        'old_path' => $oldPath,
        'ip'       => $request->ip()
      ]);

      return response()->json([
        'success'           => true,
        'message'           => 'Profile image removed.',
        'profile_image_url' => null,
        'email_initial'     => strtoupper(substr($user->email, 0, 1))
      ]);
    }
    return response()->json([
      'success' => false, 
      'message' => 'No changes made.',
      'profile_image_url' => null,
      'email_initial'     => strtoupper(substr($user->email, 0, 1))
    ],200);
  }

  public function changeBio(Request $request): JsonResponse
  {
    $user = Auth::user();
    $newBio = $request->input('bio');

    if ($user->bio === $newBio) {
      return response()->json([
        'success' => false, 
        'message' => 'No changes made.', 
        'bio' => $newBio,
      ], 200);
    }

    $validated = $this->validateJson($request, [
      'bio' => [
        'nullable', 
        'string', 
        'max:200', 
        'regex:/^[\p{L}\p{N}\s.,!?"\'\-()]+$/u'
      ],
    ]);

    $old = $user->bio;
    $bio = $validated['bio'] ?? null;
    $this->profileService->updateField($user, 'bio', $bio);
    Log::info("User #{$user->id} updated bio", ['old' => $old, 'new' => $user->bio]);

    return response()->json(['success' => true, 'bio' => $user->bio, 'message' => 'Bio Updated.']);
  }

  /**
   * Change or remove Instagram URL
   */
  public function changeInstagram(Request $request): JsonResponse
  {
    $user = Auth::user();
    $inputUrl = $request->input('instagram');

    $validated = $this->validateJson($request, [
      'instagram' => [
        'nullable', 
        'string', 
        'max:255', 
        'regex:/^([a-zA-Z0-9._-]+|https?:\/\/(www\.)?instagram\.com\/[a-zA-Z0-9._-]+\/?)$/' 
      ],
    ]);

    // 2. Specialized Update via Service (Handles normalization)
    $old = $user->instagram;
    $instagram = $validated['instagram'] ?? null;

    $this->profileService->updateInstagram($user, $instagram);
    if ($old === $user->instagram) {
      return response()->json(['success' => false, 'message' => 'No changes made.'], 200);
    }

    Log::info("User #{$user->id} updated instagram", ['old' => $old, 'new' => $user->instagram]);

    return response()->json([
      'success' => true,
      'message' => 'Instagram updated.',
      'instagram' => $user->instagram,
    ]);
  }

  /**
   * Change or remove User Location
   */
  public function changeLocation(Request $request): JsonResponse
  {
    $user = Auth::user();
    $newLocation = $request->input('user_location');

    if ($user->user_location === $newLocation) {
      return response()->json(['success' => false, 'message' => 'No changes made.'], 200);
    }

    $validated = $this->validateJson($request, [
      'user_location' => [
        'nullable', 
        'string', 
        'max:90', 
        'regex:/^[\p{L}\p{N}\s.,\-()]+$/u'
      ],
    ]);

    $old = $user->user_location;
    $location = $validated['user_location'] ?? null;
    $this->profileService->updateField($user, 'user_location', $location);
    Log::info("User #{$user->id} updated location", ['old' => $old, 'new' => $user->user_location]);

    return response()->json([
      'success' => true,
      'message' => 'Location updated.',
      'user_location' => $user->user_location,
    ]);
  }
}