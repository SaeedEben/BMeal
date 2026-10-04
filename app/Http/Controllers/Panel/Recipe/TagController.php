<?php

namespace App\Http\Controllers\Panel\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Recipe\Tag\TagIndexRequest;
use App\Http\Requests\Panel\Recipe\Tag\TagListRequest;
use App\Http\Requests\Panel\Recipe\Tag\TagStoreRequest;
use App\Http\Requests\Panel\Recipe\Tag\TagUpdateRequest;
use App\Http\Resources\Panel\Recipe\Tag\TagIndexResource;
use App\Http\Resources\Panel\Recipe\Tag\TagListResource;
use App\Http\Resources\Panel\Recipe\Tag\TagShowResource;
use App\Models\Recipe\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TagIndexRequest $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);

        $tags = Tag::query();

        if ($request->has('search')) {
            $search = '%'.$request->string('search')->trim().'%';
            $tags->where(function ($query) use ($search) {
                $query->where('name', 'like', $search);
            });
        }

        $tags = $tags->paginate($perPage)
            ->withQueryString();

        return $this->collection(TagIndexResource::collection($tags), __('responses.tags.index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TagStoreRequest $request): JsonResponse
    {
        $validated = $request->only([
            'name', 'slug', 'status',
        ]);

        try {
            $Tag = DB::transaction(function () use ($validated): Tag {
                $tag = new Tag;
                $tag->fill($validated);
                $tag->save();

                return $tag;
            });

            return $this->success($Tag, __('responses.tags.store'));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $Tag): JsonResponse
    {
        if (Gate::denies('PanelShow', $Tag)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        return $this->resource(new TagShowResource($Tag), __('responses.tags.show'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TagUpdateRequest $request, Tag $Tag): JsonResponse
    {
        $validated = $request->only([
            'name', 'slug', 'status',
        ]);

        try {
            DB::transaction(function () use ($Tag, $validated): void {
                $Tag->fill($validated);
                $Tag->save();
            });

            return $this->success($Tag, __('responses.tags.update'));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $Tag): JsonResponse
    {
        if (Gate::denies('PanelDelete', $Tag)) {
            abort(403, __('responses.errors.unauthorized'));
        }

        $Tag->delete();

        return $this->success(message: __('responses.tags.destroy'));
    }

    /**
     * List the resource.
     */
    public function list(TagListRequest $request): JsonResponse
    {
        $tags = Tag::query();

        if ($request->has('role')) {
            $tags->whereHas('roles', function ($query) use ($request) {
                $query->where('name', $request->role);
            });
        }

        $tags = $tags->get();

        return $this->collection(TagListResource::collection($tags), __('responses.tags.index'));
    }
}
