<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customer\CustomerUpdateProfileRequest;
use App\Http\Resources\Api\Customer\CustomerProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        return self::resource(CustomerProfileResource::make($user), __('responses.customer.profile'));
    }

    public function updateProfile(CustomerUpdateProfileRequest $request)
    {
        
    }
}
