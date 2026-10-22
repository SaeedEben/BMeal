<?php

namespace App\Http\Controllers\Api\Country;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Country\CountryListRequest;
use App\Http\Resources\Panel\Country\CountryListResource;
use App\Models\Country\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function list(CountryListRequest $request)
    {
        $countries = Country::query()->get();

        return $this->collection(CountryListResource::collection($countries), __('responses.api.countries.index'));
    }
}
