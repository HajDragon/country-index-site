<?php

use Livewire\Volt\Component;
use App\Services\MapDataService;
use Livewire\Attributes\Computed;

new class extends Component {
    public string $metric = 'population';

    #[Computed]
    public function mapService(): MapDataService
    {
        return app(MapDataService::class);
    }

    #[Computed]
    public function mapData()
    {
        return $this->mapService()->getMapData($this->metric);
    }

    #[Computed]
    public function colorScale()
    {
        return $this->mapService()->getColorScale($this->metric);
    }

    public function setMetric(string $metric): void
    {
        $this->metric = $metric;
        unset($this->mapData, $this->colorScale);
    }
}; ?>

<div class="relative w-full"
     x-data="worldMap({
         metric: $wire.entangle('metric'),
         mapData: @js($this->mapData),
         colorScale: @js($this->colorScale)
     })"
     x-init="initMap()">

    <!-- Metric Selector -->
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">View by:</span>
        <div class="flex gap-2">
            <flux:button
                size="sm"
                :variant="$metric === 'population' ? 'primary' : 'ghost'"
                wire:click="setMetric('population')">
                Population
            </flux:button>
            <flux:button
                size="sm"
                :variant="$metric === 'life_expectancy' ? 'primary' : 'ghost'"
                wire:click="setMetric('life_expectancy')">
                Life Expectancy
            </flux:button>
            <flux:button
                size="sm"
                :variant="$metric === 'gdp_per_capita' ? 'primary' : 'ghost'"
                wire:click="setMetric('gdp_per_capita')">
                GDP per Capita
            </flux:button>
        </div>
    </div>

    <!-- Map Container -->
    <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div
            x-ref="mapContainer"
            class="h-[600px] w-full"
            wire:ignore>
        </div>

        <!-- Loading Overlay -->
        <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-900/80">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
            Legend - <span x-text="metricLabel"></span>
        </div>
        <div class="flex items-center gap-2">
            <template x-for="(color, index) in colorScale.colors" :key="index">
                <div class="flex items-center gap-1">
                    <div
                        class="h-4 w-8 rounded border border-gray-300"
                        :style="`background-color: ${color}`">
                    </div>
                    <template x-if="index < colorScale.breaks.length">
                        <span class="text-xs text-gray-600 dark:text-gray-400"
                              x-text="formatBreak(colorScale.breaks[index])">
                        </span>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

@assets
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endassets

@script
<script>
Alpine.data('worldMap', (config) => ({
    map: null,
    markers: [],
    loading: false,
    metric: config.metric,
    mapData: config.mapData,
    colorScale: config.colorScale,

    get metricLabel() {
        const labels = {
            'population': 'Population',
            'life_expectancy': 'Life Expectancy',
            'gdp_per_capita': 'GDP per Capita'
        };
        return labels[this.metric] || this.metric;
    },

    initMap() {
        // Initialize Leaflet map
        this.map = L.map(this.$refs.mapContainer, {
            center: [20, 0],
            zoom: 2,
            minZoom: 2,
            maxZoom: 10,
            worldCopyJump: true
        });

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(this.map);

        // Plot markers
        this.plotMarkers();

        // Watch for metric changes
        this.$watch('metric', () => {
            this.loading = true;
            setTimeout(() => {
                this.plotMarkers();
                this.loading = false;
            }, 500);
        });
    },

    plotMarkers() {
        // Clear existing markers
        this.markers.forEach(marker => marker.remove());
        this.markers = [];

        // Add new markers
        this.mapData.forEach(country => {
            if (country.lat && country.lng) {
                const color = this.getColorForValue(country.value);

                const marker = L.circleMarker([country.lat, country.lng], {
                    radius: this.getRadiusForValue(country.value),
                    fillColor: color,
                    color: '#fff',
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.7
                }).addTo(this.map);

                // Add popup
                marker.bindPopup(`
                    <div class="p-2">
                        <div class="font-bold text-lg mb-1">${country.name}</div>
                        <div class="text-sm text-gray-600">
                            <strong>${this.metricLabel}:</strong> ${country.formatted_value}
                        </div>
                        <a href="/country/${country.code}"
                           class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                            View Details →
                        </a>
                    </div>
                `);

                this.markers.push(marker);
            }
        });
    },

    getColorForValue(value) {
        const { colors, breaks } = this.colorScale;

        for (let i = breaks.length - 1; i >= 0; i--) {
            if (value >= breaks[i]) {
                return colors[i + 1];
            }
        }

        return colors[0];
    },

    getRadiusForValue(value) {
        // Normalize radius based on metric
        if (this.metric === 'population') {
            if (value > 500000000) return 15;
            if (value > 100000000) return 12;
            if (value > 50000000) return 10;
            if (value > 10000000) return 8;
            return 5;
        }

        return 8; // Default radius for other metrics
    },

    formatBreak(value) {
        if (this.metric === 'population') {
            return (value / 1000000).toFixed(0) + 'M';
        } else if (this.metric === 'gdp_per_capita') {
            return '$' + (value / 1000).toFixed(0) + 'K';
        }
        return value.toString();
    }
}));
</script>
@endscript
