<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $typeFilter = $request->filled('type') && in_array($request->input('type'), Tool::RESOURCE_TYPES, true)
            ? $request->input('type')
            : null;

        $query = Category::query()
            ->withCount(['tools' => function ($query): void {
                $query->where('status', 'approved');
            }])
            ->orderBy('name');

        if ($typeFilter) {
            $query->whereHas('tools', function ($q) use ($typeFilter): void {
                $q->where('status', 'approved')->where('type', $typeFilter);
            });
        }

        $categories = $query->get();

        return view('categories.index', [
            'categories' => $categories,
            'currentType' => $typeFilter,
        ]);
    }
}
