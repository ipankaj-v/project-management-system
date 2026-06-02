<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $roles = [
            ['name' => 'owner', 'display_name' => 'Owner', 'description' => 'Organization owner with full permissions', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Can manage projects and members', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'manager', 'display_name' => 'Manager', 'description' => 'Manages projects and tasks', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'member', 'display_name' => 'Member', 'description' => 'Regular team member', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'guest', 'display_name' => 'Guest', 'description' => 'Limited access', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('roles')->upsert($roles, ['name']);
    }
}
