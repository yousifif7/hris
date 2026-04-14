<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\JsonResponse;

class JobCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            JobCategory::where('is_active', true)->orderBy('name')->get(['id', 'name'])
        );
    }
}
