<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customer\CustomerUpdateProfileRequest;
use App\Http\Resources\Api\Customer\CustomerProfileResource;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function profile()
    {
        /** @var \App\Models\User\User $user */
        $user = Auth::user();

        $user->load('categories', 'settings');

        return self::resource(CustomerProfileResource::make($user), __('responses.api.customer.profile'));
    }

    public function updateProfile(CustomerUpdateProfileRequest $request)
    {
        $auth = Auth::user();

        $user = User::findorFail($auth->id);
        $user->update($request->validated());

        return self::resource(CustomerProfileResource::make($user), __('responses.api.customer.profile_updated'));
    }
}
