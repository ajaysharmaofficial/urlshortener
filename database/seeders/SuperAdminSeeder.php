<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $email = 'superadmin@gmail.com';

        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => $email,
                'password' => Hash::make('12345678'),
                'email_verified_at' => $now,
                'remember_token' => Str::random(10),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('SuperAdmin');
            return;
        }

        $roleId = DB::table('roles')->where('name', 'SuperAdmin')->value('id');
        if ($roleId) {
            $exists = DB::table('model_has_roles')
                ->where('role_id', $roleId)
                ->where('model_type', User::class)
                ->where('model_id', $user->id)
                ->exists();

            if (!$exists) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => User::class,
                    'model_id' => $user->id,
                ]);
            }
        }
    }
}