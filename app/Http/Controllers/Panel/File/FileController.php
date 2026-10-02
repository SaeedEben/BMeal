<?php

namespace App\Http\Controllers\Panel\File;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\File\File;
use App\Http\Requests\Panel\File\FileStoreRequest;
use App\Http\Resources\Panel\File\FileShowResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
   
    /**
     * Store a newly created resource in storage.
     */
    public function store(FileStoreRequest $request): JsonResponse
    {
        try {

            $uploadedFile = $request->file('file');

            $path = $uploadedFile->store('files', 'local');

            $file = File::create([
                'name'              => $request->validated('name'),
                'file_type'         => $request->validated('file_type'),
                'mime_type'         => $uploadedFile->getMimeType(),
                'size'              => $uploadedFile->getSize(),
                'storage_path'      => $path,
                'storage_provider'  => config('filesystems.default'),
                'user_id'           => $request->user()->id,
            ]);

            return $this->success($file, 'File created successfully');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file): void
    {
        $this->resource(
            new FileShowResource($file),
            'File Show',
            200
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file): void
    {
        try {

            if ($file->storage_path && Storage::exists($file->storage_path)) {
                Storage::delete($file->storage_path);
            }

            $file->delete();

            $this->success(message: 'Successfully Deleted');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());

            $this->error($exception->getMessage());
        }
    }

}
