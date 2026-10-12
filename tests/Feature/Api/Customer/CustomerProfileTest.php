<?php

namespace Tests\Feature\Api\Customer;

use App\Models\File\File;
use App\Models\User\Role;
use App\Models\User\Setting;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_photo_replacement_is_private_and_only_available_to_its_owner(): void
    {
        Storage::fake('local');
        $customerRole = Role::query()->create(['name' => 'customer', 'guard_name' => 'web']);
        $user = $this->createCustomer('owner@example.com', $customerRole);
        $otherUser = $this->createCustomer('other@example.com', $customerRole);
        $oldPath = 'profiles/'.$user->id.'/old-avatar.png';
        Storage::disk('local')->put($oldPath, 'old avatar');
        $oldAvatar = File::query()->create([
            'name' => 'old-avatar.png',
            'file_type' => 'profile_photo',
            'mime_type' => 'image/png',
            'size' => 10,
            'storage_path' => $oldPath,
            'storage_provider' => 'local',
        ]);
        $user->avatar = $oldAvatar->getKey();
        $user->save();

        $response = $this->actingAs($user, 'sanctum')->put('/api/v1/customer/profile', [
            'file' => UploadedFile::fake()->image('new-avatar.png'),
        ]);

        $response->assertOk();
        $newAvatar = $user->fresh()->avatarFile;
        $this->assertNotNull($newAvatar);
        $this->assertSame('profile_photo', $newAvatar->getRawOriginal('file_type'));
        $this->assertStringStartsWith('profiles/'.$user->id.'/', $newAvatar->storage_path);
        $this->assertTrue(Storage::disk('local')->exists($newAvatar->storage_path));
        $this->assertFalse(Storage::disk('local')->exists($oldPath));
        $this->assertDatabaseMissing('files', ['id' => $oldAvatar->id]);
        $response->assertJsonPath('data.avatar', route('api.customer.profile.avatar'));

        $user->refresh();
        $this->actingAs($user, 'sanctum')
            ->get('/api/v1/customer/profile/avatar')
            ->assertOk();

        $this->actingAs($otherUser, 'sanctum')
            ->get('/api/v1/customer/profile/avatar')
            ->assertNotFound();
    }

    private function createCustomer(string $email, Role $role): User
    {
        $user = User::query()->create([
            'full_name' => 'Test Customer',
            'email' => $email,
            'password' => 'password',
        ]);
        $user->assignRole($role);
        Setting::query()->create(['user_id' => $user->id]);

        return $user;
    }
}
