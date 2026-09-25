<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FlightTrackingService
{
    private const OPENSKY_ENDPOINT = 'https://opensky-network.org/api/states/all';
    private const FR24_ENDPOINT = 'https://data-cloud.flightradar24.com/zones/fcgi/feed.js';
    private const CACHE_TTL_SECONDS = 12; // Cache to stay well within rate limits

    /**
     * Known airline callsign prefixes in Indonesian and Southeast Asian airspace
     */
    private const AIRLINE_PREFIXES = [
        'GIA' => ['name' => 'Garuda Indonesia', 'country' => 'Indonesia'],
        'LNI' => ['name' => 'Lion Air', 'country' => 'Indonesia'],
        'CTV' => ['name' => 'Citilink', 'country' => 'Indonesia'],
        'BTK' => ['name' => 'Batik Air', 'country' => 'Indonesia'],
        'AWQ' => ['name' => 'Indonesia AirAsia', 'country' => 'Indonesia'],
        'SJV' => ['name' => 'Super Air Jet', 'country' => 'Indonesia'],
        'SJY' => ['name' => 'Sriwijaya Air', 'country' => 'Indonesia'],
        'NAM' => ['name' => 'NAM Air', 'country' => 'Indonesia'],
        'WNG' => ['name' => 'Wings Air', 'country' => 'Indonesia'],
        'TNU' => ['name' => 'TransNusa', 'country' => 'Indonesia'],
        'PLM' => ['name' => 'Pelita Air', 'country' => 'Indonesia'],
        'PAS' => ['name' => 'Pacific Royale', 'country' => 'Indonesia'],
        'SIA' => ['name' => 'Singapore Airlines', 'country' => 'Singapore'],
        'MAS' => ['name' => 'Malaysia Airlines', 'country' => 'Malaysia'],
        'AXM' => ['name' => 'AirAsia', 'country' => 'Malaysia'],
        'SCO' => ['name' => 'Scoot', 'country' => 'Singapore'],
        'THA' => ['name' => 'Thai Airways', 'country' => 'Thailand'],
        'QFA' => ['name' => 'Qantas', 'country' => 'Australia'],
        'JST' => ['name' => 'Jetstar', 'country' => 'Australia'],
        'UAE' => ['name' => 'Emirates', 'country' => 'UAE'],
        'QTR' => ['name' => 'Qatar Airways', 'country' => 'Qatar'],
        'CPA' => ['name' => 'Cathay Pacific', 'country' => 'Hong Kong'],
    ];

    /**
     * Fetch live flights within the configured bounding box.
     *
     * @param array|null $customBbox Optional [lamin, lomin, lamax, lomax]
     * @return array
     */
    public function getLiveFlights(?array $customBbox = null): array
    {
        $bbox = $customBbox ?? config('services.flight_tracking.opensky.bbox', [
            'lamin' => -11.5,
            'lomin' => 94.5,
            'lamax' => 6.5,
            'lomax' => 141.5,
        ]);

        $cacheKey = sprintf(
            'live_flights_v2_%.2f_%.2f_%.2f_%.2f',
            $bbox['lamin'],
            $bbox['lomin'],
            $bbox['lamax'],
            $bbox['lomax']
        );

        // Check fresh cache
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            $cached['from_cache'] = true;
            return $cached;
        }

        $provider = strtolower(config('services.flight_tracking.provider', 'opensky'));

        // If provider is flightradar24 or default, fetch from FR24
        if ($provider === 'flightradar24' || $provider === 'fr24') {
            return $this->fetchFromFlightRadar24($bbox, $cacheKey);
        }

        // Try OpenSky first; if network timeout/offline occurs, automatically failover to FR24 live feed
        $openSkyResult = $this->fetchFromOpenSky($bbox, $cacheKey);

        if ($openSkyResult['status'] === 'network_error' || $openSkyResult['status'] === 'error') {
            Log::info('OpenSky Network unreachable, falling back to FlightRadar24 live ADS-B feed.');
            $fr24Result = $this->fetchFromFlightRadar24($bbox, $cacheKey);
            if ($fr24Result['status'] === 'live') {
                return $fr24Result;
            }
        }

        return $openSkyResult;
    }

    /**
     * Execute HTTP request to FlightRadar24 live ADS-B feed.
     */
    public function fetchFromFlightRadar24(array $bbox, string $cacheKey): array
    {
        // FR24 bounds format: maxLat,minLat,minLng,maxLng
        $boundsParam = sprintf('%.2f,%.2f,%.2f,%.2f', $bbox['lamax'], $bbox['lamin'], $bbox['lomin'], $bbox['lomax']);

        try {
            $client = Http::timeout(6)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                    'Accept'     => 'application/json',
                ]);

            if (file_exists('C:\\laragon\\etc\\ssl\\cacert.pem')) {
                $client = $client->withOptions(['verify' => 'C:\\laragon\\etc\\ssl\\cacert.pem']);
            }

            $response = $client->get(self::FR24_ENDPOINT, [
                'bounds' => $boundsParam,
            ]);

            if (!$response->successful()) {
                return [
                    'status'       => 'error',
                    'message'      => 'Server API penerbangan merespons dengan HTTP ' . $response->status(),
                    'total_count'  => 0,
                    'flights'      => [],
                    'last_updated' => now()->format('H:i:s \W\I\B'),
                ];
            }

            $data = $response->json();
            $formattedFlights = [];

            if (is_array($data)) {
                foreach ($data as $key => $val) {
                    if (!is_array($val) || count($val) < 14) {
                        continue;
                    }

                    $icao24    = strtoupper(trim((string) ($val[0] ?? $key)));
                    $lat       = (float) ($val[1] ?? 0);
                    $lng       = (float) ($val[2] ?? 0);
                    $heading   = (float) ($val[3] ?? 0);
                    $altFt     = (int) ($val[4] ?? 0);
                    $speedKts  = (int) ($val[5] ?? 0);
                    $model     = trim((string) ($val[8] ?? ''));
                    $reg       = trim((string) ($val[9] ?? ''));
                    $depIata   = strtoupper(trim((string) ($val[11] ?? '')));
                    $arrIata   = strtoupper(trim((string) ($val[12] ?? '')));
                    $flightNo  = strtoupper(trim((string) ($val[13] ?? '')));
                    $onGround  = (bool) ($val[14] ?? false);
                    $vSpeedFpm = (int) ($val[15] ?? 0);
                    $callsign  = strtoupper(trim((string) ($val[16] ?? '')));
                    $airlineCode = strtoupper(trim((string) ($val[18] ?? '')));

                    if ($lat === 0.0 && $lng === 0.0) {
                        continue;
                    }

                    $displayCallsign = $callsign ?: ($flightNo ?: $reg ?: $icao24);

                    // Flight status calculation
                    $status = 'Cruising';
                    if ($onGround || $altFt === 0) {
                        $status = 'On Ground';
                    } elseif ($vSpeedFpm > 250) {
                        $status = 'Climbing';
                    } elseif ($vSpeedFpm < -250) {
                        $status = 'Descending';
                    }

                    $airlineInfo = $this->detectAirline($callsign ?: $airlineCode);
                    $airlineName = $airlineInfo['name'] !== 'N/A' ? $airlineInfo['name'] : ($airlineCode ?: 'N/A');

                    $formattedFlights[] = [
                        'id'             => strtolower($icao24),
                        'icao24'         => $icao24,
                        'callsign'       => $displayCallsign,
                        'flight_number'  => $flightNo ?: 'N/A',
                        'display_name'   => $displayCallsign,
                        'lat'            => $lat,
                        'lng'            => $lng,
                        'altitude_ft'    => $altFt > 0 ? $altFt : 0,
                        'altitude_m'     => $altFt > 0 ? round($altFt * 0.3048) : 0,
                        'speed_kts'      => $speedKts,
                        'speed_kmh'      => round($speedKts * 1.852),
                        'heading'        => round($heading, 1),
                        'vertical_rate'  => $vSpeedFpm !== 0 ? round($vSpeedFpm * 0.00508, 1) : 0,
                        'on_ground'      => $onGround,
                        'status'         => $status,
                        'origin_country' => $airlineInfo['country'] !== 'N/A' ? $airlineInfo['country'] : 'Indonesia',
                        'airline'        => $airlineName,
                        'departure'      => $depIata ?: 'N/A',
                        'arrival'        => $arrIata ?: 'N/A',
                        'aircraft_type'  => $model ?: 'N/A',
                        'registration'   => $reg ?: 'N/A',
                        'last_contact'   => now()->format('H:i:s'),
                    ];
                }
            }

            $result = [
                'status'       => 'live',
                'provider'     => 'FlightRadar24 Live ADS-B',
                'message'      => 'Koneksi API flight tracking aktif & real-time.',
                'total_count'  => count($formattedFlights),
                'flights'      => $formattedFlights,
                'last_updated' => now()->format('H:i:s \W\I\B'),
                'from_cache'   => false,
            ];

            Cache::put($cacheKey, $result, self::CACHE_TTL_SECONDS);
            Cache::put($cacheKey . '_stale', $result, 60);

            return $result;

        } catch (\Throwable $e) {
            Log::error('FlightRadar24 feed exception: ' . $e->getMessage());

            $stale = Cache::get($cacheKey . '_stale');
            if ($stale) {
                $stale['status'] = 'network_error';
                $stale['message'] = 'Koneksi terganggu. Menampilkan data radar terakhir.';
                return $stale;
            }

            return [
                'status'       => 'network_error',
                'message'      => 'Gagal mengambil data live tracking: ' . $e->getMessage(),
                'total_count'  => 0,
                'flights'      => [],
                'last_updated' => now()->format('H:i:s \W\I\B'),
            ];
        }
    }

    /**
     * Execute HTTP request to OpenSky Network API.
     */
    private function fetchFromOpenSky(array $bbox, string $cacheKey): array
    {
        $username = config('services.flight_tracking.opensky.username') ?: config('services.flight_tracking.api_key');
        $password = config('services.flight_tracking.opensky.password');

        $params = [
            'lamin' => $bbox['lamin'],
            'lomin' => $bbox['lomin'],
            'lamax' => $bbox['lamax'],
            'lomax' => $bbox['lomax'],
        ];

        try {
            $client = Http::timeout(5)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'ACE-AirNav-FlightTracker/1.0',
                    'Accept'     => 'application/json',
                ]);

            if (file_exists('C:\\laragon\\etc\\ssl\\cacert.pem')) {
                $client = $client->withOptions(['verify' => 'C:\\laragon\\etc\\ssl\\cacert.pem']);
            }

            if ($username && $password) {
                $client = $client->withBasicAuth($username, $password);
            }

            $response = $client->get(self::OPENSKY_ENDPOINT, $params);

            if ($response->status() === 429) {
                return [
                    'status'       => 'rate_limited',
                    'provider'     => 'OpenSky Network',
                    'message'      => 'API OpenSky sedang mencapai batas frekuensi rate limit.',
                    'total_count'  => 0,
                    'flights'      => [],
                    'last_updated' => now()->format('H:i:s \W\I\B'),
                ];
            }

            if (!$response->successful()) {
                return [
                    'status'       => 'error',
                    'provider'     => 'OpenSky Network',
                    'message'      => 'Server API OpenSky merespons dengan HTTP ' . $response->status(),
                    'total_count'  => 0,
                    'flights'      => [],
                    'last_updated' => now()->format('H:i:s \W\I\B'),
                ];
            }

            $data = $response->json();
            $rawStates = $data['states'] ?? [];
            $formattedFlights = [];

            foreach ($rawStates as $state) {
                if (!isset($state[5], $state[6]) || $state[5] === null || $state[6] === null) {
                    continue;
                }

                $icao24   = strtolower(trim((string) ($state[0] ?? '')));
                $callsign = strtoupper(trim((string) ($state[1] ?? '')));
                $lng      = (float) $state[5];
                $lat      = (float) $state[6];

                if (empty($icao24) || $lat === 0.0 && $lng === 0.0) {
                    continue;
                }

                $baroAltMeters = $state[7] !== null ? (float) $state[7] : null;
                $onGround      = (bool) ($state[8] ?? false);
                $velocityMps   = $state[9] !== null ? (float) $state[9] : null;
                $heading       = $state[10] !== null ? round((float) $state[10], 1) : 0;
                $vertRateMps   = $state[11] !== null ? (float) $state[11] : null;
                $originCountry = trim((string) ($state[2] ?? ''));

                $altitudeFt = $baroAltMeters !== null ? round($baroAltMeters * 3.28084) : null;
                $altitudeM  = $baroAltMeters !== null ? round($baroAltMeters) : null;
                $speedKts   = $velocityMps !== null ? round($velocityMps * 1.94384) : null;
                $speedKmh   = $velocityMps !== null ? round($velocityMps * 3.6) : null;

                $flightStatus = 'Cruising';
                if ($onGround) {
                    $flightStatus = 'On Ground';
                } elseif ($vertRateMps !== null) {
                    if ($vertRateMps > 1.5) {
                        $flightStatus = 'Climbing';
                    } elseif ($vertRateMps < -1.5) {
                        $flightStatus = 'Descending';
                    }
                }

                $airlineInfo = $this->detectAirline($callsign);

                $formattedFlights[] = [
                    'id'             => $icao24,
                    'icao24'         => strtoupper($icao24),
                    'callsign'       => $callsign ?: 'N/A',
                    'flight_number'  => $callsign ?: 'N/A',
                    'display_name'   => $callsign ? ($callsign . ' (' . strtoupper($icao24) . ')') : strtoupper($icao24),
                    'lat'            => $lat,
                    'lng'            => $lng,
                    'altitude_ft'    => $altitudeFt,
                    'altitude_m'     => $altitudeM,
                    'speed_kts'      => $speedKts,
                    'speed_kmh'      => $speedKmh,
                    'heading'        => $heading,
                    'vertical_rate'  => $vertRateMps !== null ? round($vertRateMps, 1) : null,
                    'on_ground'      => $onGround,
                    'status'         => $flightStatus,
                    'origin_country' => $originCountry ?: 'N/A',
                    'airline'        => $airlineInfo['name'] ?? 'N/A',
                    'departure'      => 'N/A',
                    'arrival'        => 'N/A',
                    'aircraft_type'  => 'N/A',
                    'registration'   => 'N/A',
                    'last_contact'   => isset($state[4]) ? date('H:i:s', $state[4]) : date('H:i:s'),
                ];
            }

            $result = [
                'status'       => 'live',
                'provider'     => 'OpenSky Network',
                'message'      => 'Koneksi API OpenSky Network normal.',
                'total_count'  => count($formattedFlights),
                'flights'      => $formattedFlights,
                'last_updated' => now()->format('H:i:s \W\I\B'),
                'from_cache'   => false,
            ];

            Cache::put($cacheKey, $result, self::CACHE_TTL_SECONDS);
            Cache::put($cacheKey . '_stale', $result, 60);

            return $result;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'status'       => 'network_error',
                'provider'     => 'OpenSky Network',
                'message'      => 'Koneksi ke OpenSky Network timed out / offline.',
                'total_count'  => 0,
                'flights'      => [],
                'last_updated' => now()->format('H:i:s \W\I\B'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'       => 'error',
                'provider'     => 'OpenSky Network',
                'message'      => 'Error: ' . $e->getMessage(),
                'total_count'  => 0,
                'flights'      => [],
                'last_updated' => now()->format('H:i:s \W\I\B'),
            ];
        }
    }

    /**
     * Detect airline name and country from callsign prefix.
     */
    private function detectAirline(string $callsign): array
    {
        if (strlen($callsign) >= 3) {
            $prefix = substr($callsign, 0, 3);
            if (isset(self::AIRLINE_PREFIXES[$prefix])) {
                return self::AIRLINE_PREFIXES[$prefix];
            }
        }

        return ['name' => 'N/A', 'country' => 'N/A'];
    }
}
