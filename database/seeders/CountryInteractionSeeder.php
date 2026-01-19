<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\CountryInteraction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CountryInteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a sample of countries
        $countries = Country::inRandomOrder()->limit(50)->get();

        // Get all users (or create some if none exist)
        $users = User::all();
        if ($users->isEmpty()) {
            $users = collect([
                User::factory()->create(['email' => 'user1@example.com']),
                User::factory()->create(['email' => 'user2@example.com']),
                User::factory()->create(['email' => 'user3@example.com']),
            ]);
        }

        $interactionTypes = ['view', 'search', 'compare', 'favorite'];
        $sessionIds = ['session_'.uniqid(), 'session_'.uniqid(), 'session_'.uniqid()];

        // Create interactions spanning the last 30 days
        foreach ($countries as $country) {
            // Random number of interactions per country (1-50)
            $interactionCount = rand(1, 50);

            for ($i = 0; $i < $interactionCount; $i++) {
                CountryInteraction::create([
                    'user_id' => $users->random()->id,
                    'country_id' => $country->Code,
                    'interaction_type' => $interactionTypes[array_rand($interactionTypes)],
                    'session_id' => $sessionIds[array_rand($sessionIds)],
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                ]);
            }
        }

        // Create some trending countries (more recent activity)
        $trendingCountries = $countries->random(10);
        foreach ($trendingCountries as $country) {
            for ($i = 0; $i < rand(20, 40); $i++) {
                CountryInteraction::create([
                    'user_id' => $users->random()->id,
                    'country_id' => $country->Code,
                    'interaction_type' => $interactionTypes[array_rand($interactionTypes)],
                    'session_id' => $sessionIds[array_rand($sessionIds)],
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                ]);
            }
        }

        $this->command->info('Created '.CountryInteraction::count().' country interactions');
    }
}
