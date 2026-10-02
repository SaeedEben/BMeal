<?php

namespace App\Http\Controllers\Panel\Country;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Country\CountryIndexRequest;
use App\Http\Requests\Panel\Country\CountryStoreRequest;
use App\Http\Requests\Panel\Country\CountryUpdateRequest;
use App\Http\Requests\Panel\Country\CountryListRequest;
use App\Http\Resources\Panel\Country\CountryIndexResource;
use App\Http\Resources\Panel\Country\CountryShowResource;
use App\Http\Resources\Panel\Country\CountryListResource;
use Illuminate\Support\Facades\Gate;
use App\Models\Country\Country;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    /**
     * Display a listing of the countries.
     */
    public function index(CountryIndexRequest $request)
    {
        $perPage = $request->integer('per_page', 10);

        $countries = Country::query()->paginate($perPage)
            ->withQueryString();

        return $this->collection(CountryIndexResource::collection($$countries), __('responses.countries.index'));
    }

    /**
     * Store a newly created country.
     */
    public function store(CountryStoreRequest $request)
    {
        $validated = $request->only([
            'email', 'full_name',
            'role_id'
        ]);

        try {

            $country = new Country();
            $country->fill($validated);
            $country->save();

            return $this->success($country, __('responses.countries.store'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Display the specified country.
     */
    public function show(Country $country)
    {
        if (Gate::denies('PanelDelete', $country)) {
            abort(403, __('responses.unauthorized'));
        }

        return $this->resource(new CountryShowResource($country), __('responses.countries.show'));
    }

    /**
     * Update the specified country.
     */
    public function update(Country $country, CountryUpdateRequest $request)
    {
        $validated = $request->only([
            'email', 'full_name',
            'role_id'
        ]);

        try {

            $country->fill($validated);
            $country->update();

            return $this->success($country, __('responses.countries.update'));

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->error($exception->getMessage());
        }
    }

    /**
     * Remove the specified country.
     */
    public function destroy(Country $country)
    {
        if (Gate::denies('PanelDelete', $country)) {
            abort(403, __('responses.unauthorized'));
        }

        $country->delete();

        return $this->success(message: __('responses.countries.destroy'));
    }

    /**
     * List the countries.
     */
    public function list(CountryListRequest $request)
    {
        $countries = Country::query()->get();

        return $this->collection(CountryListResource::collection($countries), __('responses.countries.list'));
    }
}
