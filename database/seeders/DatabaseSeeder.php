<?php

namespace Database\Seeders;

use App\Models\Attendant;
use App\Models\Conference;
use App\Models\Talk;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Conference::factory()
            ->count(10)
            ->hasTalks(5)
            ->hasAttendants(75)
            ->create();
    }
}
