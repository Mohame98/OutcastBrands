<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Brand;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // GET /brands/{brand}/comments
    public function getCommentsApi(Brand $brand, Request $request)
    {
        $user = auth()->user();
   
        $filter = $request->input('filter', 'all');
        $sortBy = $request->input('sort', 'most liked');
       
        $commentsQuery = $brand->comments()
            ->with(['user', 'likes']);

        if ($filter === 'liked') {
            $commentsQuery->whereHas('likes', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($request->has('sort')) {
            if ($sortBy === 'most liked') {
                $commentsQuery->withCount('likes as likes_count')
                    ->orderByDesc('likes_count'); 
            } elseif ($sortBy === 'oldest') {
                $commentsQuery->orderBy('created_at', 'asc');  
            } elseif ($sortBy === 'newest') {
                $commentsQuery->orderBy('created_at', 'desc');
            }
        }
    
        $comments = $commentsQuery->paginate(5);

        $htmlComments = $comments->map(function ($comment) {
            return view('components.comment', compact('comment'))->render();
        });

        return response()->json([
            'html_comments' => $htmlComments,
            'has_more_comments' => $comments->hasMorePages(),
        ]);
    }

    // POST /brands/{brand}/comments
    public function addComment(Request $request, Brand $brand)
    {
        $request->validate([
            'add_comment_text' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'brand_id' => $brand->id,
            'comment_text' => $request->input('add_comment_text'),
        ]);

        $comment->load('user');
        $brand->refresh();

        $html = view('components.comment', compact('comment'))->render();

        return response()->json([
            'success' => true,
            'html_comment' => $html,
            'message' => "Comment Added",
            'comments_count' => $brand->comments_count,
        ]);
    }

    public function likeComments(Comment $comment)
    {
        $user = auth()->user();
        $existingLike = $comment->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
            $message = 'Like Removed';
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
            $liked = true;
            $message = 'Comment Liked';
        }

        $comment->loadCount('likes');

        return response()->json([
            'message' => $message,
            'likes_count' => $comment->likes_count,
            'liked' => $liked,
        ]);
    }

    public function editComment(Request $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'comment_text' => 'required|string|max:1000',
        ]);

        if (trim($comment->comment_text) === trim($validated['comment_text'])) {
            return response()->json([
                'success' => false,
                // 'message' => 'No changes detected in the comment.',
            ], 200);
        }

        $comment->update($validated);

        return response()->json([
            'success' => true,
            'comment_id' => $comment->id,
            'html_comment' => view('components.comment', compact('comment'))->render(),
        ]);
    }

    public function deleteComment(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $brand = $comment->brand;

        $comment->delete();
        $brand->refresh();

        return response()->json([
            'success' => true, 
            'message' => "Comment Deleted",
            'comment_id' => $comment->id,
            'comments_count' => $brand->comments_count,
        ]);
    }
}

