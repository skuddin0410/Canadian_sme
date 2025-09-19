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
                [ 'name' => 'Keynotes & Vision',            'slug' => 'keynotes-vision',            'type' => 'track' ],
                [ 'name' => 'Leadership & Strategy',        'slug' => 'leadership-strategy',        'type' => 'track' ],
                [ 'name' => 'Startup & Investment',         'slug' => 'startup-investment',         'type' => 'track' ],
                [ 'name' => 'Product Management',           'slug' => 'product-management',         'type' => 'track' ],
                [ 'name' => 'Design & UX',                  'slug' => 'design-ux',                  'type' => 'track' ],
                [ 'name' => 'Engineering & Architecture',   'slug' => 'engineering-architecture',   'type' => 'track' ],
                [ 'name' => 'Generative AI & LLMs',         'slug' => 'genai-llms',                 'type' => 'track' ],
                [ 'name' => 'Data & Analytics',             'slug' => 'data-analytics',             'type' => 'track' ],
                [ 'name' => 'MLOps & Model Ops',            'slug' => 'mlops-modelops',             'type' => 'track' ],
                [ 'name' => 'Cloud & Infrastructure',       'slug' => 'cloud-infrastructure',       'type' => 'track' ],
                [ 'name' => 'DevOps & Platform Eng',        'slug' => 'devops-platform',            'type' => 'track' ],
                [ 'name' => 'Cybersecurity & Privacy',      'slug' => 'cybersecurity-privacy',      'type' => 'track' ],
                [ 'name' => 'Blockchain & Web3',            'slug' => 'blockchain-web3',            'type' => 'track' ],
                [ 'name' => 'FinTech & Payments',           'slug' => 'fintech-payments',           'type' => 'track' ],
                [ 'name' => 'HealthTech & Bio',             'slug' => 'healthtech-bio',             'type' => 'track' ],
                [ 'name' => 'EdTech & Learning',            'slug' => 'edtech-learning',            'type' => 'track' ],
                [ 'name' => 'Retail & E-commerce',          'slug' => 'retail-ecommerce',           'type' => 'track' ],
                [ 'name' => 'SaaS & B2B Growth',            'slug' => 'saas-b2b-growth',            'type' => 'track' ],
                [ 'name' => 'Marketing & Growth',           'slug' => 'marketing-growth',           'type' => 'track' ],
                [ 'name' => 'Sales & GTM',                  'slug' => 'sales-gtm',                  'type' => 'track' ],
                [ 'name' => 'Customer Success',             'slug' => 'customer-success',           'type' => 'track' ],
                [ 'name' => 'Sustainability & Climate',     'slug' => 'sustainability-climate',     'type' => 'track' ],
                [ 'name' => 'GovTech & Policy',             'slug' => 'govtech-policy',             'type' => 'track' ],
                [ 'name' => 'IoT & Edge',                   'slug' => 'iot-edge',                   'type' => 'track' ],
                [ 'name' => 'Robotics & Automation',        'slug' => 'robotics-automation',        'type' => 'track' ],
                [ 'name' => 'AR/VR & Spatial',              'slug' => 'ar-vr-spatial',              'type' => 'track' ],
                [ 'name' => 'Mobile & Frontend',            'slug' => 'mobile-frontend',            'type' => 'track' ],
                [ 'name' => 'Open Source',                  'slug' => 'open-source',                'type' => 'track' ],
                [ 'name' => 'Women in Tech & DEI',          'slug' => 'women-tech-dei',             'type' => 'track' ],
            ];

           foreach ($tracks as $track) {
                Track::create([
                    'name' => $track['name'],
                    'slug' => Str::slug($track['name'])
                ]);
            }
    }
}
