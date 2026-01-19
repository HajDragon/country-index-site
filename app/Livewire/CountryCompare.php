<?php

namespace App\Livewire;

use App\Events\CountryInteracted;
use App\Models\Country;
use App\Traits\HasHomeNavigation;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;

class CountryCompare extends Component
{
    use HasHomeNavigation;

    #[Url(as: 'codes')]
    public string $codesCsv = '';

    public string $query = '';

    public function addCode(string $code): void
    {
        $normalized = Str::of($code)->upper()->substr(0, 3)->toString();
        $codes = $this->codes();
        if (! in_array($normalized, $codes, true)) {
            $codes[] = $normalized;

            // Track comparison interaction
            $country = Country::where('Code', $normalized)->first();
            if ($country) {
                CountryInteracted::dispatch($country, 'compare', auth()->user());
            }
        }
        // cap at 3
        $this->codesCsv = collect($codes)->take(3)->join(',');
    }

    public function removeCode(string $code): void
    {
        $normalized = Str::of($code)->upper()->substr(0, 3)->toString();
        $this->codesCsv = collect($this->codes())
            ->reject(fn ($c) => $c === $normalized)
            ->join(',');
    }

    public function clear(): void
    {
        $this->codesCsv = '';
        $this->query = '';
    }

    /**
     * @return array<int, string>
     */
    public function codes(): array
    {
        return collect(explode(',', $this->codesCsv))
            ->filter()
            ->map(fn ($c) => Str::of($c)->upper()->substr(0, 3)->toString())
            ->unique()
            ->values()
            ->all();
    }

    public function render()
    {
        $codes = $this->codes();

        $countries = collect();
        if (count($codes) > 0) {
            $countries = Country::query()
                ->with(['capitalCity', 'languages'])
                ->withCount('cities')
                ->whereIn('Code', array_slice($codes, 0, 3))
                ->orderByRaw("FIELD(Code, '".implode("','", array_slice($codes, 0, 3))."')")
                ->get();
        }

        $suggestions = collect();
        if (strlen($this->query) >= 2) {
            $q = '%'.str_replace('%', '\\%', $this->query).'%';
            $suggestions = Country::query()
                ->where('Name', 'like', $q)
                ->orWhere('Code', 'like', $q)
                ->orderBy('Name')
                ->limit(8)
                ->get(['Code', 'Name', 'Code2']);
        }

        return view('livewire.country-compare', [
            'countries' => $countries,
            'suggestions' => $suggestions,
            'selectedCodes' => $codes,
        ]);
    }
}
