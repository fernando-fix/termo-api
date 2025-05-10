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
                'word' => 'coisa',
                'length' => 5,
                'definition' => 'Tudo aquilo que existe ou pode ser pensado, sem nome ou classificação específica definida.',
                'is_verified' => true,
                'is_valid' => true
            ]
        ];

        foreach ($words as $word) {
            Word::create($word);
        }
    }
}
