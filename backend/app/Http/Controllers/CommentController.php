<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a new comment/review for a tool. One review per user per tool.
     */
    public function store(Request $request, Tool $tool): RedirectResponse
    {
        // README: comments/ratings only on approved tools
        if (! $tool->is_approved || $tool->status !== 'approved') {
            return back()->with('error', __('You can only leave reviews on approved tools.'));
        }

        if (auth()->id() === $tool->user_id) {
            return back()->with('error', __('Не можете да оценявате собствен инструмент.'));
        }

        if (Comment::where('user_id', auth()->id())->where('tool_id', $tool->id)->exists()) {
            return back()->with('error', __('You have already left a review for this tool.'));
        }

        $request->validate([
            'comment_text' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'tool_id' => $tool->id,
            'comment_text' => $request->input('comment_text'),
            'rating' => (int) $request->input('rating'),
        ]);

        return back()->with('success', __('Your review has been submitted.'));
    }

    /**
     * Delete the user's own comment. After delete, they can leave a new review.
     */
    public function destroy(Tool $tool, Comment $comment): RedirectResponse
    {
        if ($comment->tool_id !== $tool->id || $comment->user_id !== auth()->id()) {
            abort(403, __('You can only delete your own review.'));
        }

        $comment->delete();

        return back()->with('success', __('Your review has been deleted. You can leave a new one.'));
    }
}
