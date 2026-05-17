<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrainingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Training::with('employee');
        if ($request->filled('employee_id')) $query->where('employee_id', $request->employee_id);
        if ($request->get('expiring')) {
            $query->where('is_completed', false)->where('due_date', '<=', now()->addDays(30));
        } elseif ($request->has('completed') && $request->get('completed') === '0') {
            $query->where('is_completed', false);
        }
        return response()->json($query->orderBy('due_date')->paginate($request->get('per_page', 50)));
    }

    public function show(Training $training): JsonResponse
    {
        return response()->json($this->formatTraining($training->load('employee')));
    }

    /** Employee portal — own trainings only */
    public function portalIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        $emp = $user?->employee;
        if (! $emp) return response()->json([]);
        return response()->json(
            Training::where('employee_id', $emp->id)
                ->orderBy('due_date')
                ->get()
                ->map(fn (Training $training) => $this->formatTraining($training))
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'name'             => 'required|string',
            'due_date'         => 'nullable|date',
            'certificate_path' => 'nullable|string',
            'file'             => 'nullable|file|max:20480',
        ]);

        $training = Training::create([
            'employee_id'      => $data['employee_id'],
            'name'             => $data['name'],
            'due_date'         => $data['due_date'] ?? null,
            'certificate_path' => $data['certificate_path'] ?? null,
        ]);

        if ($request->hasFile('file')) {
            $training->certificate_path = $this->storeCertificateFile($request->file('file'), $training->employee_id);
            $training->save();
        }

        return response()->json($this->formatTraining($training->load('employee')), 201);
    }

    public function update(Request $request, Training $training): JsonResponse
    {
        $data = $request->validate([
            'name'             => 'nullable|string',
            'due_date'         => 'nullable|date',
            'certificate_path' => 'nullable|string',
            'is_completed'     => 'nullable|boolean',
            'remove_certificate' => 'nullable|boolean',
            'file'               => 'nullable|file|max:20480',
        ]);

        $updates = [];
        foreach (['name', 'due_date', 'certificate_path', 'is_completed'] as $key) {
            if ($request->has($key)) {
                $updates[$key] = $data[$key] ?? null;
            }
        }

        if (array_key_exists('is_completed', $updates)) {
            $updates['completed_date'] = $updates['is_completed'] ? now() : null;
        }

        if (($data['remove_certificate'] ?? false) && ! $request->hasFile('file')) {
            $this->deleteCertificateFile($training->certificate_path);
            $updates['certificate_path'] = null;
        }

        if ($request->hasFile('file')) {
            $this->deleteCertificateFile($training->certificate_path);
            $updates['certificate_path'] = $this->storeCertificateFile($request->file('file'), $training->employee_id);
        }

        if ($updates !== []) {
            $training->update($updates);
        }

        return response()->json($this->formatTraining($training->load('employee')));
    }

    public function destroy(Training $training): JsonResponse
    {
        $this->deleteCertificateFile($training->certificate_path);
        $training->delete();
        return response()->json(['ok' => true]);
    }

    public function complete(Training $training): JsonResponse
    {
        $training->update(['is_completed' => true, 'completed_date' => now()]);
        return response()->json($this->formatTraining($training->load('employee')));
    }

    protected function formatTraining(Training $training): array
    {
        $data = $training->toArray();
        $data['certificate_url'] = $training->certificate_path ? url($training->certificate_path) : null;
        return $data;
    }

    protected function storeCertificateFile($file, int $employeeId): string
    {
        $relativeDir = "trainings/{$employeeId}";
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($baseName) ?: 'training-file';
        $ext = $file->getClientOriginalExtension();
        $filename = time().'_'.Str::random(8).'_'.$safeName.($ext ? '.'.$ext : '');

        $file->move(public_path($relativeDir), $filename);

        return "{$relativeDir}/{$filename}";
    }

    protected function deleteCertificateFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        $absolute = public_path($path);
        if (file_exists($absolute)) {
            @unlink($absolute);
        }
    }
}
