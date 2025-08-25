<?php

namespace Database\Seeders;

use App\Models\Track;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TrackSeeder extends Seeder
{
    public function run(): void
    {
        $tracks = [
            [
                'name' => 'Backend Development',
                'description' => 'Covers APIs, Laravel, authentication, queues, and security best practices.',
                'order' => 1,
            ],
            [
                'name' => 'Frontend Development',
                'description' => 'Focuses on React, Vue, UX/UI, and building modern SPAs.',
                'order' => 2,
            ],
            [
                'name' => 'DevOps & Cloud',
                'description' => 'CICD pipelines, Docker, Kubernetes, AWS, and scaling Laravel apps.',
                'order' => 3,
            ],
            [
                'name' => 'AI & Machine Learning',
                'description' => 'Sessions on Generative AI, ML models, and Laravel + AI integrations.',
                'order' => 4,
            ],
        ];

        foreach ($tracks as $t) {
            Track::create([
                'name' => $t['name'],
                'slug' => Str::slug($t['name']),
                'description' => $t['description'],
                'order' => $t['order'],
            ]);
        }
    }
}
