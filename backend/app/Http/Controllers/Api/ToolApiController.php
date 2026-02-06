<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolApiController extends Controller
{
    /**
     * Return all approved tools in JSON format (for integration with other modules).
     */
    public function index(): JsonResponse
    {
        $tools = Tool::where('status', 'approved')->where('is_approved', true)->get();

        return response()->json($tools);
    }

    /**
     * Create a new tool (pending approval). Used by Next.js frontend.
     * Requires auth (session); tool is created with status=pending, is_approved=false.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|string|url|max:255',
            'description' => 'required|string',
            'how_to_use' => 'required|string',
            'examples' => 'nullable|array',
            'examples.*' => 'string|max:500',
            'roles' => 'nullable|array',
            'roles.*' => 'string|max:50',
            'recommended_role' => 'nullable|string|in:Backend,Frontend,QA,Design,PM',
        ]);

        $toolData = [
            'name' => $validated['name'],
            'link' => $validated['link'],
            'description' => $validated['description'],
            'how_to_use' => $validated['how_to_use'],
            'examples' => $validated['examples'] ?? [],
            'roles' => $validated['roles'] ?? [],
            'recommended_role' => $request->filled('recommended_role') ? $validated['recommended_role'] : null,
            'type' => 'tool',
            'status' => 'pending',
            'is_approved' => false,
            'is_active' => true,
            'user_id' => Auth::id(),
        ];

        $tool = Tool::create($toolData);

        ToolSubmission::create([
            'user_id' => Auth::id(),
            'tool_id' => $tool->id,
        ]);

        return response()->json($tool, 201);
    }
}
