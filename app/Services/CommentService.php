<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentService
{
  /**
   * Fetch paginated comments with optimized eager loading.
   */
  public function getPaginatedComments($query, int $perPage = 5): array
  {
    $comments = $query->with('user')
      ->withCount('likes')
      ->withAuthLikeStatus() 
      ->paginate($perPage);

    $html = $comments->getCollection()->map(function ($comment) {
      return $this->renderComment($comment);
    });

    return [
      'html_comments'      => $html,
      'has_more_comments'  => $comments->hasMorePages(),
      'total'              => $comments->total(),
      'current_page'       => $comments->currentPage(),
    ];
  }

  /**
   * Render a single comment model into HTML.
   * 
   * @param Comment $comment
   * @return string
   */
  public function renderComment(Comment $comment): string
  {
    //Ensure relationships required by the Blade component are loaded
    $comment->loadMissing('user');
    
    if (!isset($comment->likes_count)) {
      $comment->loadCount('likes');
    }

    return View::make('components.comment', [
      'comment' => $comment
    ])->render();
  }

  /**
   * Transform a collection of comment models into an array of HTML strings.
   * 
   * @param iterable $comments
   * @return \Illuminate\Support\Collection
   */
  protected function renderCommentCollection(iterable $comments)
  {
    return collect($comments)->map(fn($comment) => $this->renderComment($comment));
  }
}