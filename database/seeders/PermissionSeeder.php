<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superPermissions = [
            'companies.view',
            'companies.create',
            'companies.update',
            'companies.delete',
            'urls.view',
            'invites.view',
            'invites.create',
            'invites.delete',
            'dashboard.view',
        ];

        $adminPermissions = [
            'urls.view',
            'urls.create',
            'urls.update',
            'urls.delete',
            'invites.view',
            'invites.create',
            'invites.delete',
            'dashboard.view',
        ];

        $memberPermissions = [
            'urls.view',
            'urls.create',
            'urls.update',
            'urls.delete',
            'dashboard.view',
        ];

        $allPermissions = array_unique(array_merge(
            $superPermissions,
            $adminPermissions,
            $memberPermissions
        ));


        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web',
            ]);
        }


        $super = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $member = Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);


        $super->syncPermissions($superPermissions);
        $admin->syncPermissions($adminPermissions);
        $member->syncPermissions($memberPermissions);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}