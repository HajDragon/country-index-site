<?php

namespace App\Actions;

use App\Models\Country;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;

class ExportCountriesPdf
{
    public function execute(?Builder $query = null): \Barryvdh\DomPDF\PDF
    {
        $countries = ($query ?? Country::query())->get();

        return Pdf::loadView('exports.countries-pdf', [
            'countries' => $countries,
            'totalCount' => $countries->count(),
            'exportedAt' => now()->format('M d, Y H:i'),
        ])->setPaper('a4', 'landscape');
    }
}
