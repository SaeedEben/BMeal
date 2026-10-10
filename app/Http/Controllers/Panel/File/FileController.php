<?php

namespace App\Http\Controllers\Panel\File;

use App\Enum\File\TypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\File\FileStoreRequest;
use App\Http\Resources\Panel\File\FileShowResource;
use App\Models\File\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(FileStoreRequest $request): JsonResponse
    {
        try {

            $uploadedFile = $request->file('file');
            $fileType = $request->validated('file_type');

            [$directory, $disk] = match ($fileType) {
                TypeEnum::COUNTRY_FLAG->value => ['flags', 'public'],
                TypeEnum::MEAL_PHOTO->value => ['meals', 'public'],
                default => ['files', 'local'],
            };

            $path = $uploadedFile->store($directory, $disk);

            $file = File::create([
                'name'             => $uploadedFile->getClientOriginalName(),
                'file_type'        => $fileType,
                'mime_type'        => $uploadedFile->getMimeType(),
                'size'             => $uploadedFile->getSize(),
                'storage_path'     => $path,
                'storage_provider' => $disk,
                'user_id'          => $request->user()->id,
            ]);

            return $this->success(['id' => $file->id, 'preview' => $file->preview()], __('responses.files.store'));

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file): JsonResponse
    {
        if (Gate::denies('PanelShow', $file)) {
            abort(403, __('responses.unauthorized'));
        }

        return $this->resource(new FileShowResource($file), __('responses.files.show'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file): JsonResponse
    {
        if (Gate::denies('PanelDelete', $file)) {
            abort(403, __('responses.unauthorized'));
        }

        try {

            $storage = Storage::disk($file->disk());

            if ($file->storage_path && $storage->exists($file->storage_path)) {
                $storage->delete($file->storage_path);
            }

            $file->delete();

            return $this->success(message: __('responses.files.destroy'));

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }
}
