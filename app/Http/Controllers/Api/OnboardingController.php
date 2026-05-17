<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\OnboardingTask;
use App\Models\OnboardingTemplate;
use App\Services\CandidateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class OnboardingController extends Controller
{
    public function __construct(protected CandidateService $service) {}

    public function index(): JsonResponse
    {
        $candidates = Candidate::whereIn('status', ['onboarding', 'offer_accepted'])
            ->with(['onboardingTasks', 'latestOffer', 'category'])
            ->get();

        // Auto-create tasks for any candidate that doesn't have them yet
        foreach ($candidates as $c) {
            if ($c->onboardingTasks->isEmpty()) {
                $this->service->createOnboardingTasks($c);
                $c->load('onboardingTasks');
            }
        }

        $result = $candidates->map(fn($c) => [
            'candidate' => $c,
            'progress'  => $c->onboardingProgress(),
        ]);

        return response()->json($result);
    }

    public function completeTask(Request $request, OnboardingTask $task): JsonResponse
    {
        $task->update([
            'is_completed' => !$task->is_completed,
            'completed_at' => !$task->is_completed ? now() : null,
        ]);

        if ($request->hasFile('document')) {
            // Store uploaded onboarding documents directly under public/onboarding/{candidate_id}
            $dir = public_path("onboarding/{$task->candidate_id}");
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }

            $file = $request->file('document');
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
            $file->move($dir, $filename);

            $path = "onboarding/{$task->candidate_id}/{$filename}";
            $task->update(['document_path' => $path]);
        }

        return response()->json([
            'task'     => $task->fresh(),
            'progress' => $task->candidate->onboardingProgress(),
        ]);
    }

    public function ensureTasks(Request $request, Candidate $candidate): JsonResponse
    {
        $validated = $request->validate([
            'task_names' => 'required|array|min:1',
            'task_names.*' => 'required|string|max:255',
        ]);

        $existing = $candidate->onboardingTasks()->pluck('task_name')->map(fn ($n) => mb_strtolower($n))->all();
        $maxSort = (int) ($candidate->onboardingTasks()->max('sort_order') ?? 0);
        $created = [];

        foreach ($validated['task_names'] as $name) {
            if (in_array(mb_strtolower($name), $existing, true)) {
                continue;
            }

            $maxSort++;
            $task = $candidate->onboardingTasks()->create([
                'task_name' => $name,
                'sort_order' => $maxSort,
                'is_completed' => false,
            ]);

            $created[] = $task;
            $existing[] = mb_strtolower($name);
        }

        return response()->json([
            'created' => $created,
            'progress' => $candidate->fresh('onboardingTasks')->onboardingProgress(),
        ]);
    }

    public function templates(): JsonResponse
    {
        return response()->json(OnboardingTemplate::orderBy('sort_order')->get());
    }

    public function storeTemplate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'       => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);
        return response()->json(OnboardingTemplate::create($data), 201);
    }

    public function updateTemplate(Request $request, OnboardingTemplate $template): JsonResponse
    {
        $data = $request->validate([
            'name'       => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);
        $template->update(array_filter($data, fn($v) => $v !== null));
        return response()->json($template);
    }

    public function destroyTemplate(OnboardingTemplate $template): JsonResponse
    {
        $template->delete();
        return response()->json(['ok' => true]);
    }

    public function destroyTask(OnboardingTask $task): JsonResponse
    {
        $task->delete();
        return response()->json(['ok' => true]);
    }
}
