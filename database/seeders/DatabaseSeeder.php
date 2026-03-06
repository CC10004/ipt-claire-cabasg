<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create roles
        $adminRole = Role::create(['name' => 'Admin']);
        $studentRole = Role::create(['name' => 'Student']);

        // Create permissions (optional)
        $updateUsers = Permission::create(['name' => 'update users']);
        $deleteUsers = Permission::create(['name' => 'delete users']);

        // Assign permissions to roles
        $adminRole->givePermissionTo([$updateUsers, $deleteUsers]);

        // Optionally assign a role to the test user
        $user = User::find(1);
        $user->assignRole('Admin'); // or 'Chairman', 'User'
    }
}