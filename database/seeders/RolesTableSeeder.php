<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $roles = [
            ['name' => 'SuperAdmin', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Admin', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Member', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('roles')->insertOrIgnore($roles);
    }
}
