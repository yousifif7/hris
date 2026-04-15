<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            JobCategory::where('is_active', true)->orderBy('name')->get(['id', 'name'])
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => 'required|string|unique:job_categories,name']);
        // generate a slug from the name (database requires a non-null unique slug)
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = true;
        return response()->json(JobCategory::create($data), 201);
    }

    public function update(Request $request, JobCategory $jobCategory): JsonResponse
    {
        $data = $request->validate([
            'name'      => 'nullable|string|unique:job_categories,name,'.$jobCategory->id,
            'is_active' => 'nullable|boolean',
        ]);
        // if the name is being changed, update the slug as well
        if(isset($data['name']) && $data['name'] !== null){
            $data['slug'] = Str::slug($data['name']);
        }
        $jobCategory->update(array_filter($data, fn($v) => $v !== null));
        return response()->json($jobCategory);
    }

    public function destroy(JobCategory $jobCategory): JsonResponse
    {
        $jobCategory->update(['is_active' => false]);
        return response()->json(['ok' => true]);
    }
}
