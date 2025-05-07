<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Word;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $words = [
            [
                'word' => 'algaz',
                'length' => 5,
                'definition' => 'algaz',
                'is_verified' => true,
                'is_valid' => true
            ],
            [
                'word' => 'causa',
                'length' => 5,
                'definition' => 'causa',
                'is_verified' => true,
                'is_valid' => true
            ]
        ];

        foreach ($words as $word) {
            Word::create($word);
        }
    }
}
