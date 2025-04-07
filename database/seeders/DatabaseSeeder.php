<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        // Create 50 users and assign random roles to each
        User::factory(100)->create()->each(function ($user) {
            $role = fake()->randomElement(['admin', 'super_admin']);
            $user->assignRole($role);
        });


        $user = User::updateOrCreate(
            [
                'name' => 'Test Admin',
                'email' => 'test@admin.com',
                'password' => bcrypt('admin123'),
            ]
        );

        $user->assignRole('admin');

        $users = User::factory()->create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ]);

        $role = fake()->randomElement(['admin', 'super_admin']);
        $users->assignRole($role);
    }
}
