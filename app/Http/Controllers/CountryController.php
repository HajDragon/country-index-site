<?php

namespace App\Http\Controllers;

use App\Actions\ExportCountriesPdf;
use App\Exports\CountriesExport;
use App\Http\Requests\StorecountryRequest;
use App\Http\Requests\UpdatecountryRequest;
use App\Models\Country;
use Maatwebsite\Excel\Facades\Excel;

class CountryController extends Controller
{
    /**
     * Display the specified country view.
     */
    public function index(string $countryCode)
    {
        return view('layouts.country-view', [
            'countryCode' => $countryCode,
        ]);
    }

    /**
     * Export countries as CSV with optional filtering.
     */
    public function exportCsv()
    {
        $query = Country::query();

        // Apply filters from request if provided
        if (request()->filled('continents')) {
            $continents = explode(',', request('continents'));
            $query->whereIn('Continent', $continents);
        }

        if (request()->filled('regions')) {
            $regions = explode(',', request('regions'));
            $query->whereIn('Region', $regions);
        }

        if (request()->filled('population_min')) {
            $query->where('Population', '>=', request('population_min'));
        }

        if (request()->filled('population_max')) {
            $query->where('Population', '<=', request('population_max'));
        }

        if (request()->filled('life_expectancy_min')) {
            $query->where('LifeExpectancy', '>=', request('life_expectancy_min'));
        }

        if (request()->filled('life_expectancy_max')) {
            $query->where('LifeExpectancy', '<=', request('life_expectancy_max'));
        }

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'like', "%{$search}%")
                    ->orWhere('Region', 'like', "%{$search}%");
            });
        }

        $filename = 'countries-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(new CountriesExport($query), $filename);
    }

    /**
     * Export countries as PDF with optional filtering.
     */
    public function exportPdf()
    {
        $query = Country::query();

        // Apply same filters as CSV
        if (request()->filled('continents')) {
            $continents = explode(',', request('continents'));
            $query->whereIn('Continent', $continents);
        }

        if (request()->filled('regions')) {
            $regions = explode(',', request('regions'));
            $query->whereIn('Region', $regions);
        }

        if (request()->filled('population_min')) {
            $query->where('Population', '>=', request('population_min'));
        }

        if (request()->filled('population_max')) {
            $query->where('Population', '<=', request('population_max'));
        }

        if (request()->filled('life_expectancy_min')) {
            $query->where('LifeExpectancy', '>=', request('life_expectancy_min'));
        }

        if (request()->filled('life_expectancy_max')) {
            $query->where('LifeExpectancy', '<=', request('life_expectancy_max'));
        }

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('Name', 'like', "%{$search}%")
                    ->orWhere('Region', 'like', "%{$search}%");
            });
        }

        $pdf = (new ExportCountriesPdf)->execute($query);
        $filename = 'countries-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCountryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(country $country)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(country $country)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatecountryRequest $request, country $country)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(country $country)
    {
        //
    }
}
