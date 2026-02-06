<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\ToolSubmission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard with submissions and user management (no tool lists).
     */
    public function index(): View
    {
        $submissions = ToolSubmission::query()
            ->with(['user', 'tool'])
            ->latest()
            ->take(30)
            ->get();

        $users = User::query()
            ->orderBy('name')
            ->paginate(20, ['*'], 'users_page');

        return view('admin.dashboard', compact('submissions', 'users'));
    }

    /**
     * Single unified view for pending tool approval. Used by both Owner and Admin.
     */
    public function pendingTools(): View
    {
        $pendingTools = Tool::query()
            ->with(['user', 'category'])
            ->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('is_approved', false);
            })
            ->latest()
            ->get();

        return view('admin.tools.pending', compact('pendingTools'));
    }

    /**
     * Update a tool's status (approve or reject). Approve sets status to 'approved' and is_approved to true.
     * Allowed for Owner or Admin role only.
     */
    public function toggleStatus(Request $request, Tool $tool): RedirectResponse
    {
        Gate::authorize('approve', $tool);

        $request->validate([
            'status' => 'required|string|in:approved,rejected',
        ]);

        $tool->update([
            'status' => $request->input('status'),
            'is_approved' => $request->input('status') === 'approved',
        ]);
        Cache::forget('approved_tools');

        return back()->with('success', __('Tool status updated to :status.', ['status' => $request->input('status')]));
    }
}
