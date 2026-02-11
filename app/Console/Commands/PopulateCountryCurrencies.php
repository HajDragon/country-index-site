<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PopulateCountryCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'countries:populate-currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate currency codes and names for countries from REST Countries API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Fetching currency data from REST Countries API...');

        try {
            $response = Http::withOptions([
                'verify' => config('app.env') === 'production',
            ])->timeout(30)->get('https://restcountries.com/v3.1/all', [
                'fields' => 'cca3,currencies',
            ]);

            if (! $response->successful()) {
                $this->error('Failed to fetch data from REST Countries API');

                return self::FAILURE;
            }

            $countriesData = $response->json();
            $updated = 0;
            $skipped = 0;

            $this->info('Processing '.count($countriesData).' countries...');
            $bar = $this->output->createProgressBar(count($countriesData));

            foreach ($countriesData as $countryData) {
                $code = $countryData['cca3'] ?? null;
                $currencies = $countryData['currencies'] ?? [];

                if (! $code || empty($currencies)) {
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                $country = Country::where('Code', $code)->first();

                if (! $country) {
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                // Get first currency (most countries have one primary currency)
                $currencyCode = array_key_first($currencies);
                $currencyData = $currencies[$currencyCode];

                $country->update([
                    'currency_code' => $currencyCode,
                    'currency_name' => $currencyData['name'] ?? null,
                ]);

                $updated++;
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->info("Successfully updated {$updated} countries");
            $this->info("Skipped {$skipped} countries (no data or not in database)");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
