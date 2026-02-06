<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\JsonResponse;

class ToolApiController extends Controller
{
    /**
     * Return all approved tools in JSON format (for integration with other modules).
     */
    public function index(): JsonResponse
    {
        $tools = Tool::where('status', 'approved')->get();

        return response()->json($tools);
    }
}
