<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ApiValidator
{
  /**
   * Validates a request and automatically returns a 422 JSON response on failure.
   * If successful, it returns the validated data.
   */
  protected function validateJson(Request $request, array $rules): array
  {
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      throw new HttpResponseException(response()->json([
        'success' => false,
        'errors' => $validator->errors(),
      ], 422));
    }
    return $validator->validated();
  }

  /**
   * Authorizes an action and returns a 401 JSON response on failure.
   * $message is optional to prevent errors.
   */
  protected function authorizeJson(bool $condition, string $message = 'Unauthorized.'): void
  {
    if (!$condition) {
      throw new HttpResponseException(response()->json([
        'success' => false,
        'message' => $message,
        'errors'  => ['auth' => [$message]] 
      ], 401));
    }
  }

  /**
   * A helper for generic JSON exceptions
   */
  protected function throwJsonError(string $message, int $code = 400): void
  {
    throw new HttpResponseException(response()->json([
      'success' => false,
      'message' => $message,
    ], $code));
  }
}
