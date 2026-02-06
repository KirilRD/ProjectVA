<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'productivity',
            'coding',
            'free',
            'paid',
            'browser-based',
            'api',
            'writing',
            'design',
            'testing',
            'devops',
            'collaboration',
            'open-source',
            'ai',
            'no-code',
            'documentation',
        ];

        foreach ($names as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
    }
}
