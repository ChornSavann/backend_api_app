<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Super Administrator with full access']);
        Role::firstOrCreate(['name' => 'Admin'], ['description' => 'Administrator role']);
        Role::firstOrCreate(['name' => 'Cashier'], ['description' => 'Standard Cashier role']);
    }
}
