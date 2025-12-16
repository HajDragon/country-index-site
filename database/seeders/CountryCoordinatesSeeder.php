<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountryCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // All countries with their capital city coordinates
        $coordinates = [
            // North America
            'USA' => ['latitude' => 38.8951, 'longitude' => -77.0364], // Washington, D.C.
            'CAN' => ['latitude' => 45.4215, 'longitude' => -75.6972], // Ottawa
            'MEX' => ['latitude' => 19.4326, 'longitude' => -99.1332], // Mexico City
            'CUB' => ['latitude' => 23.1136, 'longitude' => -82.3666], // Havana
            'DOM' => ['latitude' => 18.4861, 'longitude' => -69.9312], // Santo Domingo
            'HTI' => ['latitude' => 18.5944, 'longitude' => -72.3074], // Port-au-Prince
            'JAM' => ['latitude' => 18.0179, 'longitude' => -76.8099], // Kingston
            'PRI' => ['latitude' => 18.4655, 'longitude' => -66.1057], // San Juan
            'TTO' => ['latitude' => 10.6918, 'longitude' => -61.2225], // Port of Spain
            'BHS' => ['latitude' => 25.0443, 'longitude' => -77.3504], // Nassau
            'BRB' => ['latitude' => 13.1939, 'longitude' => -59.5432], // Bridgetown
            'BLZ' => ['latitude' => 17.2510, 'longitude' => -88.7590], // Belmopan
            'CRI' => ['latitude' => 9.9281, 'longitude' => -84.0907], // San José
            'SLV' => ['latitude' => 13.6929, 'longitude' => -89.2182], // San Salvador
            'GTM' => ['latitude' => 14.6349, 'longitude' => -90.5069], // Guatemala City
            'HND' => ['latitude' => 14.0723, 'longitude' => -87.1921], // Tegucigalpa
            'NIC' => ['latitude' => 12.1364, 'longitude' => -86.2514], // Managua
            'PAN' => ['latitude' => 8.9824, 'longitude' => -79.5199], // Panama City

            // South America
            'BRA' => ['latitude' => -15.8267, 'longitude' => -47.9218], // Brasília
            'ARG' => ['latitude' => -34.6037, 'longitude' => -58.3816], // Buenos Aires
            'CHL' => ['latitude' => -33.4489, 'longitude' => -70.6693], // Santiago
            'COL' => ['latitude' => 4.7110, 'longitude' => -74.0721], // Bogotá
            'PER' => ['latitude' => -12.0464, 'longitude' => -77.0428], // Lima
            'VEN' => ['latitude' => 10.4806, 'longitude' => -66.9036], // Caracas
            'ECU' => ['latitude' => -0.1807, 'longitude' => -78.4678], // Quito
            'BOL' => ['latitude' => -16.5000, 'longitude' => -68.1500], // La Paz
            'PRY' => ['latitude' => -25.2637, 'longitude' => -57.5759], // Asunción
            'URY' => ['latitude' => -34.9011, 'longitude' => -56.1645], // Montevideo
            'GUY' => ['latitude' => 6.8013, 'longitude' => -58.1551], // Georgetown
            'SUR' => ['latitude' => 5.8520, 'longitude' => -55.2038], // Paramaribo
            'GUF' => ['latitude' => 4.9227, 'longitude' => -52.3269], // Cayenne

            // Europe
            'GBR' => ['latitude' => 51.5074, 'longitude' => -0.1278], // London
            'FRA' => ['latitude' => 48.8566, 'longitude' => 2.3522], // Paris
            'DEU' => ['latitude' => 52.5200, 'longitude' => 13.4050], // Berlin
            'ITA' => ['latitude' => 41.9028, 'longitude' => 12.4964], // Rome
            'ESP' => ['latitude' => 40.4168, 'longitude' => -3.7038], // Madrid
            'NLD' => ['latitude' => 52.3676, 'longitude' => 4.9041], // Amsterdam
            'BEL' => ['latitude' => 50.8503, 'longitude' => 4.3517], // Brussels
            'CHE' => ['latitude' => 46.9480, 'longitude' => 7.4474], // Bern
            'AUT' => ['latitude' => 48.2082, 'longitude' => 16.3738], // Vienna
            'SWE' => ['latitude' => 59.3293, 'longitude' => 18.0686], // Stockholm
            'NOR' => ['latitude' => 59.9139, 'longitude' => 10.7522], // Oslo
            'DNK' => ['latitude' => 55.6761, 'longitude' => 12.5683], // Copenhagen
            'FIN' => ['latitude' => 60.1699, 'longitude' => 24.9384], // Helsinki
            'POL' => ['latitude' => 52.2297, 'longitude' => 21.0122], // Warsaw
            'CZE' => ['latitude' => 50.0755, 'longitude' => 14.4378], // Prague
            'HUN' => ['latitude' => 47.4979, 'longitude' => 19.0402], // Budapest
            'ROU' => ['latitude' => 44.4268, 'longitude' => 26.1025], // Bucharest
            'GRC' => ['latitude' => 37.9838, 'longitude' => 23.7275], // Athens
            'PRT' => ['latitude' => 38.7223, 'longitude' => -9.1393], // Lisbon
            'IRL' => ['latitude' => 53.3498, 'longitude' => -6.2603], // Dublin
            'RUS' => ['latitude' => 55.7558, 'longitude' => 37.6173], // Moscow
            'UKR' => ['latitude' => 50.4501, 'longitude' => 30.5234], // Kyiv
            'BLR' => ['latitude' => 53.9006, 'longitude' => 27.5590], // Minsk
            'BGR' => ['latitude' => 42.6977, 'longitude' => 23.3219], // Sofia
            'HRV' => ['latitude' => 45.8150, 'longitude' => 15.9819], // Zagreb
            'SVK' => ['latitude' => 48.1486, 'longitude' => 17.1077], // Bratislava
            'SVN' => ['latitude' => 46.0569, 'longitude' => 14.5058], // Ljubljana
            'EST' => ['latitude' => 59.4370, 'longitude' => 24.7536], // Tallinn
            'LVA' => ['latitude' => 56.9496, 'longitude' => 24.1052], // Riga
            'LTU' => ['latitude' => 54.6872, 'longitude' => 25.2797], // Vilnius
            'SRB' => ['latitude' => 44.7866, 'longitude' => 20.4489], // Belgrade
            'BIH' => ['latitude' => 43.8564, 'longitude' => 18.4131], // Sarajevo
            'MKD' => ['latitude' => 41.9973, 'longitude' => 21.4280], // Skopje
            'ALB' => ['latitude' => 41.3275, 'longitude' => 19.8187], // Tirana
            'MDA' => ['latitude' => 47.0105, 'longitude' => 28.8638], // Chișinău
            'LUX' => ['latitude' => 49.6116, 'longitude' => 6.1319], // Luxembourg
            'ISL' => ['latitude' => 64.1466, 'longitude' => -21.9426], // Reykjavik
            'MLT' => ['latitude' => 35.8989, 'longitude' => 14.5146], // Valletta
            'MNE' => ['latitude' => 42.4304, 'longitude' => 19.2594], // Podgorica

            // Asia
            'CHN' => ['latitude' => 39.9042, 'longitude' => 116.4074], // Beijing
            'IND' => ['latitude' => 28.6139, 'longitude' => 77.2090], // New Delhi
            'JPN' => ['latitude' => 35.6762, 'longitude' => 139.6503], // Tokyo
            'KOR' => ['latitude' => 37.5665, 'longitude' => 126.9780], // Seoul
            'IDN' => ['latitude' => -6.2088, 'longitude' => 106.8456], // Jakarta
            'PAK' => ['latitude' => 33.6844, 'longitude' => 73.0479], // Islamabad
            'BGD' => ['latitude' => 23.8103, 'longitude' => 90.4125], // Dhaka
            'THA' => ['latitude' => 13.7563, 'longitude' => 100.5018], // Bangkok
            'VNM' => ['latitude' => 21.0285, 'longitude' => 105.8542], // Hanoi
            'MYS' => ['latitude' => 3.1390, 'longitude' => 101.6869], // Kuala Lumpur
            'SGP' => ['latitude' => 1.3521, 'longitude' => 103.8198], // Singapore
            'PHL' => ['latitude' => 14.5995, 'longitude' => 120.9842], // Manila
            'MMR' => ['latitude' => 16.8661, 'longitude' => 96.1951], // Naypyidaw
            'KHM' => ['latitude' => 11.5564, 'longitude' => 104.9282], // Phnom Penh
            'LAO' => ['latitude' => 17.9757, 'longitude' => 102.6331], // Vientiane
            'NPL' => ['latitude' => 27.7172, 'longitude' => 85.3240], // Kathmandu
            'LKA' => ['latitude' => 6.9271, 'longitude' => 79.8612], // Colombo
            'AFG' => ['latitude' => 34.5553, 'longitude' => 69.2075], // Kabul
            'IRN' => ['latitude' => 35.6892, 'longitude' => 51.3890], // Tehran
            'IRQ' => ['latitude' => 33.3128, 'longitude' => 44.3615], // Baghdad
            'TUR' => ['latitude' => 39.9334, 'longitude' => 32.8597], // Ankara
            'ISR' => ['latitude' => 31.7683, 'longitude' => 35.2137], // Jerusalem
            'JOR' => ['latitude' => 31.9454, 'longitude' => 35.9284], // Amman
            'LBN' => ['latitude' => 33.8886, 'longitude' => 35.4955], // Beirut
            'SYR' => ['latitude' => 33.5138, 'longitude' => 36.2765], // Damascus
            'YEM' => ['latitude' => 15.5527, 'longitude' => 48.5164], // Sana'a
            'OMN' => ['latitude' => 23.5880, 'longitude' => 58.3829], // Muscat
            'ARE' => ['latitude' => 24.4539, 'longitude' => 54.3773], // Abu Dhabi
            'SAU' => ['latitude' => 24.7136, 'longitude' => 46.6753], // Riyadh
            'KWT' => ['latitude' => 29.3117, 'longitude' => 47.4818], // Kuwait City
            'QAT' => ['latitude' => 25.2854, 'longitude' => 51.5310], // Doha
            'BHR' => ['latitude' => 26.0667, 'longitude' => 50.5577], // Manama
            'KAZ' => ['latitude' => 51.1694, 'longitude' => 71.4491], // Astana
            'UZB' => ['latitude' => 41.2995, 'longitude' => 69.2401], // Tashkent
            'TKM' => ['latitude' => 37.9601, 'longitude' => 58.3261], // Ashgabat
            'KGZ' => ['latitude' => 42.8746, 'longitude' => 74.5698], // Bishkek
            'TJK' => ['latitude' => 38.5598, 'longitude' => 68.7870], // Dushanbe
            'MNG' => ['latitude' => 47.8864, 'longitude' => 106.9057], // Ulaanbaatar
            'PRK' => ['latitude' => 39.0392, 'longitude' => 125.7625], // Pyongyang
            'TWN' => ['latitude' => 25.0330, 'longitude' => 121.5654], // Taipei
            'HKG' => ['latitude' => 22.3193, 'longitude' => 114.1694], // Hong Kong
            'MAC' => ['latitude' => 22.1987, 'longitude' => 113.5439], // Macau
            'ARM' => ['latitude' => 40.1792, 'longitude' => 44.4991], // Yerevan
            'AZE' => ['latitude' => 40.4093, 'longitude' => 49.8671], // Baku
            'GEO' => ['latitude' => 41.7151, 'longitude' => 44.8271], // Tbilisi
            'BTN' => ['latitude' => 27.4728, 'longitude' => 89.6393], // Thimphu
            'MDV' => ['latitude' => 4.1755, 'longitude' => 73.5093], // Malé
            'BRN' => ['latitude' => 4.9031, 'longitude' => 114.9398], // Bandar Seri Begawan
            'TLS' => ['latitude' => -8.5569, 'longitude' => 125.5603], // Dili

            // Africa
            'ZAF' => ['latitude' => -25.7479, 'longitude' => 28.2293], // Pretoria
            'EGY' => ['latitude' => 30.0444, 'longitude' => 31.2357], // Cairo
            'NGA' => ['latitude' => 9.0765, 'longitude' => 7.3986], // Abuja
            'KEN' => ['latitude' => -1.2921, 'longitude' => 36.8219], // Nairobi
            'ETH' => ['latitude' => 9.0320, 'longitude' => 38.7469], // Addis Ababa
            'GHA' => ['latitude' => 5.6037, 'longitude' => -0.1870], // Accra
            'MAR' => ['latitude' => 34.0209, 'longitude' => -6.8416], // Rabat
            'DZA' => ['latitude' => 36.7538, 'longitude' => 3.0588], // Algiers
            'TUN' => ['latitude' => 36.8065, 'longitude' => 10.1815], // Tunis
            'LBY' => ['latitude' => 32.8872, 'longitude' => 13.1913], // Tripoli
            'SDN' => ['latitude' => 15.5007, 'longitude' => 32.5599], // Khartoum
            'TZA' => ['latitude' => -6.7924, 'longitude' => 39.2083], // Dodoma
            'UGA' => ['latitude' => 0.3476, 'longitude' => 32.5825], // Kampala
            'AGO' => ['latitude' => -8.8368, 'longitude' => 13.2343], // Luanda
            'MOZ' => ['latitude' => -25.9655, 'longitude' => 32.5832], // Maputo
            'ZMB' => ['latitude' => -15.4167, 'longitude' => 28.2833], // Lusaka
            'ZWE' => ['latitude' => -17.8252, 'longitude' => 31.0335], // Harare
            'BWA' => ['latitude' => -24.6282, 'longitude' => 25.9231], // Gaborone
            'NAM' => ['latitude' => -22.5597, 'longitude' => 17.0832], // Windhoek
            'MWI' => ['latitude' => -13.9626, 'longitude' => 33.7741], // Lilongwe
            'SEN' => ['latitude' => 14.6928, 'longitude' => -17.4467], // Dakar
            'CIV' => ['latitude' => 6.8270, 'longitude' => -5.2893], // Yamoussoukro
            'CMR' => ['latitude' => 3.8480, 'longitude' => 11.5021], // Yaoundé
            'MLI' => ['latitude' => 12.6392, 'longitude' => -8.0029], // Bamako
            'BFA' => ['latitude' => 12.3714, 'longitude' => -1.5197], // Ouagadougou
            'NER' => ['latitude' => 13.5127, 'longitude' => 2.1128], // Niamey
            'TCD' => ['latitude' => 12.1348, 'longitude' => 15.0557], // N'Djamena
            'SOM' => ['latitude' => 2.0469, 'longitude' => 45.3182], // Mogadishu
            'RWA' => ['latitude' => -1.9403, 'longitude' => 29.8739], // Kigali
            'BDI' => ['latitude' => -3.3731, 'longitude' => 29.9189], // Gitega
            'ERI' => ['latitude' => 15.3229, 'longitude' => 38.9251], // Asmara
            'DJI' => ['latitude' => 11.8251, 'longitude' => 42.5903], // Djibouti
            'GAB' => ['latitude' => 0.4162, 'longitude' => 9.4673], // Libreville
            'COG' => ['latitude' => -4.2634, 'longitude' => 15.2429], // Brazzaville
            'COD' => ['latitude' => -4.4419, 'longitude' => 15.2663], // Kinshasa
            'CAF' => ['latitude' => 4.3947, 'longitude' => 18.5582], // Bangui
            'GNQ' => ['latitude' => 3.7504, 'longitude' => 8.7371], // Malabo
            'STP' => ['latitude' => 0.3365, 'longitude' => 6.7273], // São Tomé
            'MUS' => ['latitude' => -20.1609, 'longitude' => 57.5012], // Port Louis
            'SYC' => ['latitude' => -4.6796, 'longitude' => 55.4920], // Victoria
            'MDG' => ['latitude' => -18.8792, 'longitude' => 47.5079], // Antananarivo
            'COM' => ['latitude' => -11.7172, 'longitude' => 43.2473], // Moroni
            'LSO' => ['latitude' => -29.3167, 'longitude' => 27.4833], // Maseru
            'SWZ' => ['latitude' => -26.3054, 'longitude' => 31.1367], // Mbabane
            'GIN' => ['latitude' => 9.6412, 'longitude' => -13.5784], // Conakry
            'GMB' => ['latitude' => 13.4549, 'longitude' => -16.5790], // Banjul
            'GNB' => ['latitude' => 11.8037, 'longitude' => -15.1804], // Bissau
            'SLE' => ['latitude' => 8.4657, 'longitude' => -13.2317], // Freetown
            'LBR' => ['latitude' => 6.2907, 'longitude' => -10.7605], // Monrovia
            'MRT' => ['latitude' => 18.0735, 'longitude' => -15.9582], // Nouakchott
            'BEN' => ['latitude' => 6.4969, 'longitude' => 2.6289], // Porto-Novo
            'TGO' => ['latitude' => 6.1228, 'longitude' => 1.2255], // Lomé
            'CPV' => ['latitude' => 14.9330, 'longitude' => -23.5133], // Praia

            // Oceania
            'AUS' => ['latitude' => -35.2809, 'longitude' => 149.1300], // Canberra
            'NZL' => ['latitude' => -41.2865, 'longitude' => 174.7762], // Wellington
            'PNG' => ['latitude' => -9.4438, 'longitude' => 147.1803], // Port Moresby
            'FJI' => ['latitude' => -18.1248, 'longitude' => 178.4501], // Suva
            'SLB' => ['latitude' => -9.4280, 'longitude' => 159.9495], // Honiara
            'VUT' => ['latitude' => -17.7334, 'longitude' => 168.3273], // Port Vila
            'NCL' => ['latitude' => -22.2758, 'longitude' => 166.4572], // Nouméa
            'PLW' => ['latitude' => 7.5150, 'longitude' => 134.5825], // Ngerulmud
            'GUM' => ['latitude' => 13.4443, 'longitude' => 144.7937], // Hagåtña
            'ASM' => ['latitude' => -14.2710, 'longitude' => -170.1322], // Pago Pago
            'TON' => ['latitude' => -21.1790, 'longitude' => -175.1982], // Nuku'alofa
            'WSM' => ['latitude' => -13.8314, 'longitude' => -171.7518], // Apia
            'KIR' => ['latitude' => 1.3382, 'longitude' => 173.0176], // Tarawa
            'TUV' => ['latitude' => -8.5211, 'longitude' => 179.1962], // Funafuti
            'NRU' => ['latitude' => -0.5477, 'longitude' => 166.9209], // Yaren
            'MHL' => ['latitude' => 7.1315, 'longitude' => 171.1845], // Majuro
            'FSM' => ['latitude' => 6.9248, 'longitude' => 158.1610], // Palikir
            'COK' => ['latitude' => -21.2367, 'longitude' => -159.7777], // Avarua
            'NIU' => ['latitude' => -19.0544, 'longitude' => -169.8672], // Alofi
            'TKL' => ['latitude' => -9.2002, 'longitude' => -171.8484], // Nukunonu
            'GBR' => ['latitude' => 51.5074, 'longitude' => -0.1278], // London
            'FRA' => ['latitude' => 48.8566, 'longitude' => 2.3522], // Paris
            'DEU' => ['latitude' => 52.5200, 'longitude' => 13.4050], // Berlin
            'ITA' => ['latitude' => 41.9028, 'longitude' => 12.4964], // Rome
            'ESP' => ['latitude' => 40.4168, 'longitude' => -3.7038], // Madrid
            'NLD' => ['latitude' => 52.3676, 'longitude' => 4.9041], // Amsterdam
            'BEL' => ['latitude' => 50.8503, 'longitude' => 4.3517], // Brussels
            'CHE' => ['latitude' => 46.9480, 'longitude' => 7.4474], // Bern
            'AUT' => ['latitude' => 48.2082, 'longitude' => 16.3738], // Vienna
            'SWE' => ['latitude' => 59.3293, 'longitude' => 18.0686], // Stockholm
            'NOR' => ['latitude' => 59.9139, 'longitude' => 10.7522], // Oslo
            'DNK' => ['latitude' => 55.6761, 'longitude' => 12.5683], // Copenhagen
            'FIN' => ['latitude' => 60.1699, 'longitude' => 24.9384], // Helsinki
            'POL' => ['latitude' => 52.2297, 'longitude' => 21.0122], // Warsaw
            'CZE' => ['latitude' => 50.0755, 'longitude' => 14.4378], // Prague
            'HUN' => ['latitude' => 47.4979, 'longitude' => 19.0402], // Budapest
            'ROU' => ['latitude' => 44.4268, 'longitude' => 26.1025], // Bucharest
            'GRC' => ['latitude' => 37.9838, 'longitude' => 23.7275], // Athens
            'PRT' => ['latitude' => 38.7223, 'longitude' => -9.1393], // Lisbon
            'IRL' => ['latitude' => 53.3498, 'longitude' => -6.2603], // Dublin
            'RUS' => ['latitude' => 55.7558, 'longitude' => 37.6173], // Moscow
            'UKR' => ['latitude' => 50.4501, 'longitude' => 30.5234], // Kyiv
            'TUR' => ['latitude' => 39.9334, 'longitude' => 32.8597], // Ankara
            'ISR' => ['latitude' => 31.7683, 'longitude' => 35.2137], // Jerusalem
            'SAU' => ['latitude' => 24.7136, 'longitude' => 46.6753], // Riyadh
            'ARE' => ['latitude' => 24.4539, 'longitude' => 54.3773], // Abu Dhabi
            'EGY' => ['latitude' => 30.0444, 'longitude' => 31.2357], // Cairo
            'ZAF' => ['latitude' => -25.7479, 'longitude' => 28.2293], // Pretoria
            'KEN' => ['latitude' => -1.2921, 'longitude' => 36.8219], // Nairobi
            'NGA' => ['latitude' => 9.0765, 'longitude' => 7.3986], // Abuja
            'MAR' => ['latitude' => 34.0209, 'longitude' => -6.8416], // Rabat
            'JPN' => ['latitude' => 35.6762, 'longitude' => 139.6503], // Tokyo
            'CHN' => ['latitude' => 39.9042, 'longitude' => 116.4074], // Beijing
            'KOR' => ['latitude' => 37.5665, 'longitude' => 126.9780], // Seoul
            'IND' => ['latitude' => 28.6139, 'longitude' => 77.2090], // New Delhi
            'PAK' => ['latitude' => 33.6844, 'longitude' => 73.0479], // Islamabad
            'BGD' => ['latitude' => 23.8103, 'longitude' => 90.4125], // Dhaka
            'THA' => ['latitude' => 13.7563, 'longitude' => 100.5018], // Bangkok
            'VNM' => ['latitude' => 21.0285, 'longitude' => 105.8542], // Hanoi
            'IDN' => ['latitude' => -6.2088, 'longitude' => 106.8456], // Jakarta
            'MYS' => ['latitude' => 3.1390, 'longitude' => 101.6869], // Kuala Lumpur
            'SGP' => ['latitude' => 1.3521, 'longitude' => 103.8198], // Singapore
            'PHL' => ['latitude' => 14.5995, 'longitude' => 120.9842], // Manila
            'AUS' => ['latitude' => -35.2809, 'longitude' => 149.1300], // Canberra
            'NZL' => ['latitude' => -41.2865, 'longitude' => 174.7762], // Wellington
            'BRA' => ['latitude' => -15.8267, 'longitude' => -47.9218], // Brasília
            'ARG' => ['latitude' => -34.6037, 'longitude' => -58.3816], // Buenos Aires
            'CHL' => ['latitude' => -33.4489, 'longitude' => -70.6693], // Santiago
            'MEX' => ['latitude' => 19.4326, 'longitude' => -99.1332], // Mexico City
            'COL' => ['latitude' => 4.7110, 'longitude' => -74.0721], // Bogotá
            'PER' => ['latitude' => -12.0464, 'longitude' => -77.0428], // Lima
            'VEN' => ['latitude' => 10.4806, 'longitude' => -66.9036], // Caracas
        ];

        foreach ($coordinates as $code => $coords) {
            Country::where('Code', $code)
                ->update([
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude'],
                ]);
        }

        $this->command->info('Updated coordinates for '.count($coordinates).' countries.');
    }
}
