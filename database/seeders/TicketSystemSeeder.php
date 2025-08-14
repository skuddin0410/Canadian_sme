<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TicketCategory;
use App\Models\TicketType;
use App\Models\TicketPricingRule;
use App\Models\Event;

class TicketSystemSeeder extends Seeder
{
    public function run()
    {
        // Create ticket categories
        $categories = [
            ['name' => 'General Admission', 'color' => '#007bff', 'description' => 'Standard access tickets'],
            ['name' => 'VIP', 'color' => '#ffc107', 'description' => 'Premium access with additional benefits'],
            ['name' => 'Student', 'color' => '#28a745', 'description' => 'Discounted tickets for students'],
            ['name' => 'Early Bird', 'color' => '#17a2b8', 'description' => 'Limited time discount tickets'],
            ['name' => 'Group', 'color' => '#6f42c1', 'description' => 'Bulk purchase discounts'],
        ];

        foreach ($categories as $index => $categoryData) {
            TicketCategory::create([
                'name' => $categoryData['name'],
                'slug' => \Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'color' => $categoryData['color'],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

        // Create sample events if they don't exist
        if (Event::count() == 0) {
            Event::create([
                'name' => 'Tech Conference 2025',
                'description' => 'Annual technology conference',
                'start_date' => now()->addMonths(3),
                'end_date' => now()->addMonths(3)->addDays(2),
                'is_active' => true,
            ]);
        }

        $event = Event::first();
        $categories = TicketCategory::all();

        // Create ticket types
        $ticketTypes = [
            [
                'name' => 'General Admission',
                'category_id' => $categories->where('name', 'General Admission')->first()->id,
                'base_price' => 299.00,
                'total_quantity' => 500,
                'description' => 'Access to all conference sessions and networking events',
            ],
            [
                'name' => 'VIP Pass',
                'category_id' => $categories->where('name', 'VIP')->first()->id,
                'base_price' => 599.00,
                'total_quantity' => 100,
                'description' => 'Premium access with VIP lounge, priority seating, and exclusive sessions',
            ],
            [
                'name' => 'Student Ticket',
                'category_id' => $categories->where('name', 'Student')->first()->id,
                'base_price' => 149.00,
                'total_quantity' => 200,
                'description' => 'Discounted tickets for students with valid ID',
                'requires_approval' => true,
            ],
        ];

        foreach ($ticketTypes as $typeData) {
            $ticketType = TicketType::create([
                'event_id' => $event->id,
                'category_id' => $typeData['category_id'],
                'name' => $typeData['name'],
                'slug' => \Str::slug($typeData['name']),
                'description' => $typeData['description'],
                'base_price' => $typeData['base_price'],
                'total_quantity' => $typeData['total_quantity'],
                'available_quantity' => $typeData['total_quantity'],
                'min_quantity_per_order' => 1,
                'max_quantity_per_order' => 10,
                'is_active' => true,
                'requires_approval' => $typeData['requires_approval'] ?? false,
                'sale_start_date' => now()->subDays(30),
                'sale_end_date' => now()->addMonths(2),
            ]);

            // Create pricing rules for some ticket types
            if ($ticketType->name === 'General Admission') {
                // Early bird pricing
                TicketPricingRule::create([
                    'ticket_type_id' => $ticketType->id,
                    'name' => 'Early Bird Discount',
                    'type' => 'early_bird',
                    'price' => 249.00,
                    'start_date' => now()->subDays(30),
                    'end_date' => now()->addDays(30),
                    'is_active' => true,
                ]);

                // Group discount
                TicketPricingRule::create([
                    'ticket_type_id' => $ticketType->id,
                    'name' => 'Group Discount (5+)',
                    'type' => 'group',
                    'price' => 269.00,
                    'min_quantity' => 5,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Ticket system seeded successfully!');
    }
}