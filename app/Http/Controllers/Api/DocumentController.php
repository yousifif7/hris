<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $file = $request->file('file');
        $path = $file->store("documents/{$request->documentable_type}/{$request->documentable_id}", 'private');

        $doc = $model->documents()->create([
            'name'      => $request->name ?? $file->getClientOriginalName(),
            'type'      => $request->type,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json($doc, 201);
    }

    public function download(Document $document)
    {
        return Storage::disk('private')->download($document->file_path, $document->name);
    }
}
