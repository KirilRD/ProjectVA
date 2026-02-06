<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Role;
use App\Models\Tag;
use App\Models\Tool;
use App\Models\ToolSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ToolController extends Controller
{
    public function index(Request $request): View
    {
        $hasFilters = $request->filled('search')
            || $request->filled('category_id')
            || $request->filled('type')
            || $request->filled('roles')
            || $request->filled('tags');

        if (! $hasFilters) {
            $perPage = 12;
            $page = max(1, (int) $request->input('page', 1));
            $all = Cache::remember('approved_tools', 600, function () {
                return Tool::query()
                    ->where('status', 'approved')
                    ->with(['category', 'tags', 'roles', 'user'])
                    ->withAvg('comments', 'rating')
                    ->latest()
                    ->get();
            });
            $tools = new LengthAwarePaginator(
                $all->forPage($page, $perPage),
                $all->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $query = Tool::query()
                ->where('status', 'approved')
                ->with(['category', 'tags', 'roles', 'user'])
                ->withAvg('comments', 'rating');

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->input('search') . '%');
            }
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }
            if ($request->filled('type') && in_array($request->input('type'), Tool::RESOURCE_TYPES, true)) {
                $query->where('type', $request->input('type'));
            }
            $roleIds = array_values(array_filter(array_map('intval', (array) $request->input('roles', []))));
            if (! empty($roleIds)) {
                $query->whereHas('roles', function ($q) use ($roleIds) {
                    $q->whereIn('roles.id', $roleIds);
                });
            }
            $tagIds = array_values(array_filter(array_map('intval', (array) $request->input('tags', []))));
            if (! empty($tagIds)) {
                $query->whereHas('tags', function ($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                });
            }
            $tools = $query->latest()->paginate(12)->withQueryString();
        }

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        // Map user role to tool recommended_role for "Recommended for your role" section
        $userRecommendedRole = null;
        $recommendedForRoleTools = collect();
        if (Auth::check() && $user = Auth::user()) {
            $map = [
                'backend' => 'Backend',
                'frontend' => 'Frontend',
                'qa' => 'QA',
                'designer' => 'Design',
                'project_manager' => 'PM',
            ];
            $userRecommendedRole = $map[$user->role ?? ''] ?? null;
            if ($userRecommendedRole) {
                $recommendedForRoleTools = Tool::query()
                    ->where('status', 'approved')
                    ->where('recommended_role', $userRecommendedRole)
                    ->with(['category', 'tags', 'roles', 'user'])
                    ->withAvg('comments', 'rating')
                    ->latest()
                    ->limit(12)
                    ->get();
            }
        }

        return view('tools.index', compact('tools', 'categories', 'tags', 'roles', 'userRecommendedRole', 'recommendedForRoleTools'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('tools.create', compact('categories', 'tags', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:'.implode(',', Tool::RESOURCE_TYPES),
            'link' => 'required|string|url|max:255',
            'official_docs_link' => 'nullable|string|url|max:255',
            'description' => 'required|string',
            'how_to_use' => 'required|string',
            'usage_instructions' => 'nullable|string',
            'examples' => 'nullable|array',
            'examples_link' => 'nullable|string|url|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'recommended_role' => 'nullable|string|in:Backend,Frontend,QA,Design,PM',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $toolData = collect($validated)->except(['role_ids', 'tag_ids', 'image'])->toArray();
        $toolData['user_id'] = Auth::id();
        $toolData['is_active'] = $request->boolean('is_active', true);
        $toolData['recommended_role'] = $request->filled('recommended_role') ? $validated['recommended_role'] : null;
        $toolData['type'] = $validated['type'] ?? 'tool';

        $tool = Tool::create($toolData);

        $tool->roles()->sync($validated['role_ids'] ?? []);
        $tool->tags()->sync($validated['tag_ids'] ?? []);

        if ($request->hasFile('image')) {
            $tool->addMedia($request->file('image'))->toMediaCollection('image');
        }

        ToolSubmission::create([
            'user_id' => Auth::id(),
            'tool_id' => $tool->id,
        ]);

        return redirect()->route('tools.index')->with('success', 'Tool added successfully!');
    }

    /**
     * @param  Tool  $tool  Eloquent model instance (from route model binding)
     */
    public function show(Tool $tool): View
    {
        if (! auth()->user()?->is_admin && $tool->status !== 'approved') {
            abort(404);
        }
        $tool->load(['category', 'tags', 'roles', 'user', 'screenshots', 'comments' => fn ($q) => $q->with('user')->latest()]);
        return view('tools.show', compact('tool'));
    }

    public function edit(Tool $tool): View
    {
        Gate::authorize('update', $tool);
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $tool->load(['tags', 'roles']);
        return view('tools.edit', compact('tool', 'categories', 'tags', 'roles'));
    }

    public function update(Request $request, Tool $tool): RedirectResponse
    {
        Gate::authorize('update', $tool);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:'.implode(',', Tool::RESOURCE_TYPES),
            'link' => 'required|string|url|max:255',
            'official_docs_link' => 'nullable|string|url|max:255',
            'description' => 'required|string',
            'how_to_use' => 'required|string',
            'usage_instructions' => 'nullable|string',
            'examples' => 'nullable|array',
            'examples_link' => 'nullable|string|url|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'recommended_role' => 'nullable|string|in:Backend,Frontend,QA,Design,PM',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $toolData = collect($validated)->except(['role_ids', 'tag_ids', 'image'])->toArray();
        $toolData['is_active'] = $request->boolean('is_active', true);
        $toolData['recommended_role'] = $request->filled('recommended_role') ? $validated['recommended_role'] : null;
        $toolData['type'] = $validated['type'] ?? 'tool';
        $tool->update($toolData);

        $tool->roles()->sync($validated['role_ids'] ?? []);
        $tool->tags()->sync($validated['tag_ids'] ?? []);

        if ($request->hasFile('image')) {
            $tool->addMedia($request->file('image'))->toMediaCollection('image');
        }

        return redirect()->route('tools.index')->with('status', 'Tool updated.');
    }

    public function destroy(Tool $tool): RedirectResponse
    {
        Gate::authorize('delete', $tool);
        $tool->clearMediaCollection('image');
        $tool->delete();
        return redirect()->route('tools.index')->with('success', 'Tool deleted successfully.');
    }

}
