<?php

namespace Database\Seeders;

use App\Models\PraiseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PraiseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Singer',
            'Dancer',
            'GuitarPlayer',
            'Musician',
            'Designer',
            'Actor',
            'Comedian',
            'Artist',
        ];

        foreach ($categories as $category) {
            PraiseCategory::create([
                'name' => $category,
            ]);
        }
    }
}
