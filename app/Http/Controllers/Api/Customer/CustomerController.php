<?php

namespace App\Http\Controllers\Api\Customer;

use App\Enum\File\TypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customer\CustomerUpdateProfileRequest;
use App\Http\Resources\Api\Customer\CustomerProfileResource;
use App\Models\File\File;
use App\Models\User\Setting;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function profile()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->load('categories', 'settings');

        return self::resource(CustomerProfileResource::make($user), __('responses.api.customer.profile'));
    }

    public function updateProfile(CustomerUpdateProfileRequest $request)
    {
        $auth = Auth::user();
        $data = $request->validated();

        /** @var User $user */
        $user         = User::findOrFail($auth->id);
        $oldAvatar    = $user->avatarFile;
        $uploadedFile = $request->file('file');
        $disk         = 'local';
        $path         = $uploadedFile->store('profiles/'.$user->id, $disk);

        if (! is_string($path)) {
            abort(500, 'Unable to store profile photo.');
        }

        try {
            DB::transaction(function () use ($user, $uploadedFile, $disk, $path): void {
                $avatar = File::create([
                    'name'             => $uploadedFile->getClientOriginalName(),
                    'file_type'        => TypeEnum::PROFILE_PHOTO,
                    'mime_type'        => $uploadedFile->getMimeType(),
                    'size'             => $uploadedFile->getSize(),
                    'storage_path'     => $path,
                    'storage_provider' => $disk,
                ]);

                $user->avatarFile()->associate($avatar);
                $user->save();
            });
        } catch (\Throwable $exception) {
            Storage::disk($disk)->delete($path);

            throw $exception;
        }

        if ($oldAvatar && ! User::query()->where('avatar', $oldAvatar->getKey())->exists()) {
            $oldStorage = Storage::disk($oldAvatar->disk());
            $deleted = ! $oldStorage->exists($oldAvatar->storage_path)
                || $oldStorage->delete($oldAvatar->storage_path);

            if ($deleted) {
                $oldAvatar->delete();
            }
        }

        Setting::query()->where('user_id', $user->id)->update([
            'meal_reminder' => $data['meal_reminder'] ?? $user->settings->meal_reminder,
            'shopping_list_reminder' => $data['shopping_list_reminder'] ?? $user->settings->shopping_list_reminder,
            'weakly_planning_reminder' => $data['weakly_planning_reminder'] ?? $user->settings->weakly_planning_reminder,
        ]);

        $user->categories()->sync($data['categories'] ?? []);

        return self::resource(CustomerProfileResource::make($user), __('responses.api.customer.profile_updated'));
    }

}
