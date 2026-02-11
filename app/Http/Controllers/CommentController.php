<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiValidator;

class CommentController extends Controller
{
  use ApiValidator;

  protected CommentService $commentService;

  public function __construct(CommentService $commentService)
  {
    $this->commentService = $commentService;
  }

  /**
   * GET /brands/{brand}/comments
   */
  public function getCommentsApi(Brand $brand, Request $request): JsonResponse
  {
    $query = $brand->comments()
      ->likedBy($request->filter === 'liked' ? Auth::user() : null)
      ->sortBy($request->input('sort', 'most liked'));
    return response()->json($this->commentService->getPaginatedComments($query));
  }

  /**
   * POST /brands/{brand}/comments
   */
  public function addComment(Request $request, Brand $brand): JsonResponse
  {
    $this->authorizeJson(Auth::check());

    $validated = $this->validateJson($request, [
      'add_comment_text' => 'required|string|max:400'
    ]);

    $comment = $brand->comments()->create([
      'user_id' => Auth::id(),
      'comment_text' => $validated['add_comment_text'],
    ]);

    return response()->json([
      'success'        => true,
      'message'        => "Comment Added",
      'html_comment'   => $this->commentService->renderComment($comment),
      'comments_count' => $brand->refresh()->comments_count,
    ]);
  }

  /**
   * POST /comments/{comment}/like
   */
  public function likeComments(Comment $comment): JsonResponse
  {
    $this->authorizeJson(Auth::check());

    $user = Auth::user();
    $like = $comment->likes()
      ->where('user_id', $user->id)
      ->first();

    if ($like) {
      $like->delete();
      $liked = false;
    } else {
      $comment->likes()->create(['user_id' => $user->id]);
      $liked = true;
    }

    return response()->json([
      'success' => true,
      'liked' => $liked,
      'likes_count' => $comment->likes()->count(),
    ]);
  }

  public function editComment(Request $request, Comment $comment): JsonResponse
  {
    $this->authorizeJson($comment->user_id === Auth::id());

    $validated = $this->validateJson($request, [
      'comment_text' => 'required|string|max:1000'
    ]);

    if (trim($comment->comment_text) === trim($validated['comment_text'])) {
      return response()->json(['success' => false], 200);
    }

    $comment->update($validated);

    return response()->json([
      'success'      => true,
      'comment_id'   => $comment->id,
      'html_comment' => $this->commentService->renderComment($comment),
    ]);
  }

  public function deleteComment(Comment $comment): JsonResponse
  {
    $this->authorizeJson($comment->user_id === Auth::id());
    $brand = $comment->brand;
    $commentId = $comment->id;
    $comment->delete();

    return response()->json([
      'success'        => true, 
      'comment_id'     => $commentId,
      'comments_count' => $brand->refresh()->comments_count,
    ]);
  }
}
