<?php

namespace App\Http\Controllers\Api\Country;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Country\CountryCategoryRequest;
use App\Http\Requests\Api\Country\CountryListRequest;
use App\Http\Resources\Api\Country\CountryCategoryResource;
use App\Http\Resources\Panel\Country\CountryListResource;
use App\Models\Country\Country;
use App\Models\Recipe\Recipe;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function list(CountryListRequest $request)
    {
        $countries = Country::query()->get();

        return $this->collection(CountryListResource::collection($countries), __('responses.api.countries.index'));
    }

    public function countryCategory(CountryCategoryRequest $request): JsonResponse
    {
        $countries = Country::query()
            ->whereHas('recipes.image')
            ->select('countries.*')
            ->addSelect([
                'random_recipe_id' => Recipe::query()
                    ->select('id')
                    ->whereColumn('country_id', 'countries.id')
                    ->whereHas('image')
                    ->inRandomOrder()
                    ->limit(1),
            ])
            ->inRandomOrder()
            ->limit(7)
            ->get();

        $recipes = Recipe::query()
            ->with('image')
            ->whereIn('id', $countries->pluck('random_recipe_id'))
            ->get()
            ->keyBy('id');

        foreach ($countries as $country) {
            $country->setRelation('recipes', new EloquentCollection([
                $recipes->get($country->random_recipe_id),
            ]));
        }

        return $this->collection(CountryCategoryResource::collection($countries), __('responses.api.country.category'));
    }
}
