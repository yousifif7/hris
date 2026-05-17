<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file'              => 'required|file|max:20480',
            'documentable_type' => 'required|in:candidate,employee',
            'documentable_id'   => 'required|integer',
            'type'              => 'nullable|string',
            'name'              => 'nullable|string',
        ]);

        $modelClass = $request->documentable_type === 'candidate'
            ? \App\Models\Candidate::class
            : \App\Models\Employee::class;

        $model = $modelClass::findOrFail($request->documentable_id);
        $file  = $request->file('file');

        $relative = "documents/{$request->documentable_type}/{$request->documentable_id}";
        $filename  = time().'_'.$file->getClientOriginalName();
        $file->move(public_path($relative), $filename);
        $path = "{$relative}/{$filename}";

        $doc = $model->documents()->create([
            'name'        => $request->name ?? $file->getClientOriginalName(),
            'type'        => $request->type,
            'file_path'   => $path,
            'mime_type'   => $file->getMimeType() ?? $file->getClientMimeType(),
            'file_size'   => filesize(public_path($path)),
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json(array_merge($doc->toArray(), [
            'url' => url($path),
        ]), 201);
    }

    /** Employee portal — own documents only */
    public function portalIndex(): JsonResponse
    {
        $emp = auth()->user()->employee;
        if (! $emp) return response()->json([]);
        return response()->json(
            Document::where('documentable_type', \App\Models\Employee::class)
                ->where('documentable_id', $emp->id)
                ->orderByDesc('created_at')->get()
        );
    }

    public function download(Document $document): BinaryFileResponse
    {
        return response()->download(public_path($document->file_path), $document->name);
    }

    public function destroy(Document $document): JsonResponse
    {
        $filePath = public_path($document->file_path);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        $document->delete();
        return response()->json(['ok' => true]);
    }
}
