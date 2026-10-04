<?php

namespace App\Http\Controllers\Panel\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Recipe\Category\CategoryIndexRequest;
use App\Http\Requests\Panel\Recipe\Category\CategoryListRequest;
use App\Http\Requests\Panel\Recipe\Category\CategoryStoreRequest;
use App\Http\Requests\Panel\Recipe\Category\CategoryUpdateRequest;
use App\Http\Resources\Panel\Recipe\Category\CategoryIndexResource;
use App\Http\Resources\Panel\Recipe\Category\CategoryListResource;
use App\Http\Resources\Panel\Recipe\Category\CategoryShowResource;
use App\Models\Recipe\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(CategoryIndexRequest $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);

        $categories = Category::query();
        if ($request->has('search')) {
            $search = '%'.$request->string('search')->trim().'%';
            $categories->where(function ($query) use ($search) {
                $query->where('name', 'like', $search);
            });
        }
        $categories = $categories->paginate($perPage)
            ->withQueryString();

        return $this->collection(CategoryIndexResource::collection($categories), __('responses.categories.index'));
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryStoreRequest $request)
    {
        $validated = $request->only([
            'name', 'slug',
            'status', 'description',
        ]);

        try {
            $category = DB::transaction(function () use ($validated): Category {
                $category = new Category;
                $category->fill($validated);
                $category->save();

                return $category;
            });

            return $this->success($category, __('responses.categories.store'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        if (Gate::denies('PanelDelete', $category)) {
            abort(403, __('responses.unauthorized'));
        }

        return $this->resource(new CategoryShowResource($category), __('responses.categories.show'));
    }

    /**
     * Update the specified category.
     */
    public function update(Category $category, CategoryUpdateRequest $request)
    {
        $validated = $request->only([
            'name', 'slug',
            'status', 'description',
        ]);

        try {
            DB::transaction(function () use ($category, $validated): void {
                $category->fill($validated);
                $category->save();
            });

            return $this->success($category, __('responses.categories.update'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        if (Gate::denies('PanelDelete', $category)) {
            abort(403, __('responses.unauthorized'));
        }

        $category->delete();

        return $this->success(message: __('responses.categories.destroy'));
    }

    /**
     * List the categories.
     */
    public function list(CategoryListRequest $request)
    {
        $categories = Category::query()->get();

        return $this->collection(CategoryListResource::collection($categories), __('responses.categories.index'));
    }
}
