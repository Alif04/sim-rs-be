<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $roles = ['manager', 'customer', 'doctor', 'specialist', 'patient'];
        $permission = ['create role', 'edit role', 'delete role', 'view role'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        foreach ($permission as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $managerRole = Role::where('name', 'manager')->first();
        $managerRole->givePermissionTo($permission);

        foreach ($roles as $role) {
          $user = User::factory()->create([
                'name' => ucfirst($role) . ' User',
                'email' => strtolower($role) . '@example.com',
                'phone' => fake()->phoneNumber(),
                'gender' => fake()->randomElement(['Male', 'Female']),
                'photo' => fake()->imageUrl(640, 480, 'people', true, 'profile'),
                'password'=> Hash::make('password123'),
            ]);
            $user->assignRole($role);
        }
    }
}
