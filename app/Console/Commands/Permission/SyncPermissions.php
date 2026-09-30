<?php

namespace App\Console\Commands\Permission;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:permissions')]
#[Description('Command description')]
class SyncPermissions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = config('RolePermissions');

        $allPermissions = [];
        $key            = 0;
        foreach ($file as $panel => $environment) {
            foreach ($environment as $scope => $permissions) {
                foreach ($permissions as $permission) {
//                    $allPermissions[$key]['uuid']       = (string)Str::uuid();
                    $allPermissions[$key]['name']       = "$panel.$scope.$permission";
                    $allPermissions[$key]['guard_name'] = "web";
//                    $allPermissions[$key]['created_at'] = \Illuminate\Support\now();
//                    $allPermissions[$key]['updated_at'] = \Illuminate\Support\now();
                    Permission::FirstOrCreate($allPermissions[$key]);
                    $key++;
                }
            }
        }
        $role = Role::query()->where('name', 'admin')->first();
        if (!$role) {
            $role             = new Role();
            $role->name       = 'admin';
            $role->guard_name = 'web';
            $role->save();
        }

        $createdPermissions = Permission::query()->get();

        $role->syncPermissions($createdPermissions);

        $admin = User::query()->where('phone' , 'saeed@gmail.com')->first();
        $admin->assignRole($role);
    }
}
