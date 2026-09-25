<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

/**
 * Airport Seeder
 *
 * DATA SOURCE: DEMO data — ditandai dengan flag iata_code yang jelas.
 * File Daftar_Kode_Bandara_Indonesia.xlsx belum tersedia.
 * Data koordinat diambil dari sumber publik (Wikipedia, OurAirports).
 * JANGAN menggunakan data ini sebagai referensi operasional resmi.
 */
class AirportSeeder extends Seeder
{
    public function run(): void
    {
        $airports = [
            // === JAWA ===
            [
                'iata_code'  => 'CGK',
                'icao_code'  => 'WIII',
                'name'       => 'Soekarno-Hatta International Airport',
                'city'       => 'Tangerang',
                'province'   => 'Banten',
                'country'    => 'ID',
                'latitude'   => -6.1256,
                'longitude'  => 106.6559,
                'elevation'  => 34,
                'status'     => true,
            ],
            [
                'iata_code'  => 'SUB',
                'icao_code'  => 'WARR',
                'name'       => 'Juanda International Airport',
                'city'       => 'Surabaya',
                'province'   => 'Jawa Timur',
                'country'    => 'ID',
                'latitude'   => -7.3798,
                'longitude'  => 112.7870,
                'elevation'  => 9,
                'status'     => true,
            ],
            [
                'iata_code'  => 'JOG',
                'icao_code'  => 'WARJ',
                'name'       => 'Adisutjipto International Airport',
                'city'       => 'Yogyakarta',
                'province'   => 'DI Yogyakarta',
                'country'    => 'ID',
                'latitude'   => -7.7882,
                'longitude'  => 110.4317,
                'elevation'  => 350,
                'status'     => true,
            ],
            [
                'iata_code'  => 'SOC',
                'icao_code'  => 'WARQ',
                'name'       => 'Adisumarmo International Airport',
                'city'       => 'Solo',
                'province'   => 'Jawa Tengah',
                'country'    => 'ID',
                'latitude'   => -7.5162,
                'longitude'  => 110.7572,
                'elevation'  => 128,
                'status'     => true,
            ],
            [
                'iata_code'  => 'BDO',
                'icao_code'  => 'WICC',
                'name'       => 'Husein Sastranegara International Airport',
                'city'       => 'Bandung',
                'province'   => 'Jawa Barat',
                'country'    => 'ID',
                'latitude'   => -6.9006,
                'longitude'  => 107.5762,
                'elevation'  => 740,
                'status'     => true,
            ],
            // === BALI & NUSA TENGGARA ===
            [
                'iata_code'  => 'DPS',
                'icao_code'  => 'WADD',
                'name'       => 'Ngurah Rai International Airport',
                'city'       => 'Denpasar',
                'province'   => 'Bali',
                'country'    => 'ID',
                'latitude'   => -8.7482,
                'longitude'  => 115.1671,
                'elevation'  => 14,
                'status'     => true,
            ],
            [
                'iata_code'  => 'LOP',
                'icao_code'  => 'WADL',
                'name'       => 'Lombok International Airport',
                'city'       => 'Lombok',
                'province'   => 'Nusa Tenggara Barat',
                'country'    => 'ID',
                'latitude'   => -8.7573,
                'longitude'  => 116.2766,
                'elevation'  => 52,
                'status'     => true,
            ],
            // === SUMATERA ===
            [
                'iata_code'  => 'KNO',
                'icao_code'  => 'WIMM',
                'name'       => 'Kualanamu International Airport',
                'city'       => 'Medan',
                'province'   => 'Sumatera Utara',
                'country'    => 'ID',
                'latitude'   => 3.6422,
                'longitude'  => 98.8853,
                'elevation'  => 23,
                'status'     => true,
            ],
            [
                'iata_code'  => 'PLM',
                'icao_code'  => 'WIPP',
                'name'       => 'Sultan Mahmud Badaruddin II International Airport',
                'city'       => 'Palembang',
                'province'   => 'Sumatera Selatan',
                'country'    => 'ID',
                'latitude'   => -2.8982,
                'longitude'  => 104.6997,
                'elevation'  => 37,
                'status'     => true,
            ],
            [
                'iata_code'  => 'PDG',
                'icao_code'  => 'WIPT',
                'name'       => 'Minangkabau International Airport',
                'city'       => 'Padang',
                'province'   => 'Sumatera Barat',
                'country'    => 'ID',
                'latitude'   => -0.7869,
                'longitude'  => 100.2809,
                'elevation'  => 18,
                'status'     => true,
            ],
            [
                'iata_code'  => 'BTH',
                'icao_code'  => 'WIDD',
                'name'       => 'Hang Nadim International Airport',
                'city'       => 'Batam',
                'province'   => 'Kepulauan Riau',
                'country'    => 'ID',
                'latitude'   => 1.1212,
                'longitude'  => 104.1192,
                'elevation'  => 126,
                'status'     => true,
            ],
            [
                'iata_code'  => 'PKU',
                'icao_code'  => 'WIBB',
                'name'       => 'Sultan Syarif Kasim II International Airport',
                'city'       => 'Pekanbaru',
                'province'   => 'Riau',
                'country'    => 'ID',
                'latitude'   => 0.4608,
                'longitude'  => 101.4449,
                'elevation'  => 99,
                'status'     => true,
            ],
            // === KALIMANTAN ===
            [
                'iata_code'  => 'BPN',
                'icao_code'  => 'WALL',
                'name'       => 'Sultan Aji Muhammad Sulaiman Sepinggan International Airport',
                'city'       => 'Balikpapan',
                'province'   => 'Kalimantan Timur',
                'country'    => 'ID',
                'latitude'   => -1.2683,
                'longitude'  => 116.8942,
                'elevation'  => 12,
                'status'     => true,
            ],
            [
                'iata_code'  => 'BDJ',
                'icao_code'  => 'WAOO',
                'name'       => 'Syamsudin Noor International Airport',
                'city'       => 'Banjarmasin',
                'province'   => 'Kalimantan Selatan',
                'country'    => 'ID',
                'latitude'   => -3.4424,
                'longitude'  => 114.7631,
                'elevation'  => 66,
                'status'     => true,
            ],
            // === SULAWESI ===
            [
                'iata_code'  => 'UPG',
                'icao_code'  => 'WAAA',
                'name'       => 'Sultan Hasanuddin International Airport',
                'city'       => 'Makassar',
                'province'   => 'Sulawesi Selatan',
                'country'    => 'ID',
                'latitude'   => -5.0616,
                'longitude'  => 119.5540,
                'elevation'  => 47,
                'status'     => true,
            ],
            [
                'iata_code'  => 'MDC',
                'icao_code'  => 'WAMM',
                'name'       => 'Sam Ratulangi International Airport',
                'city'       => 'Manado',
                'province'   => 'Sulawesi Utara',
                'country'    => 'ID',
                'latitude'   => 1.5495,
                'longitude'  => 124.9261,
                'elevation'  => 80,
                'status'     => true,
            ],
            // === PAPUA & MALUKU ===
            [
                'iata_code'  => 'DJJ',
                'icao_code'  => 'WAJJ',
                'name'       => 'Sentani International Airport',
                'city'       => 'Jayapura',
                'province'   => 'Papua',
                'country'    => 'ID',
                'latitude'   => -2.5769,
                'longitude'  => 140.5164,
                'elevation'  => 289,
                'status'     => true,
            ],
            [
                'iata_code'  => 'AMQ',
                'icao_code'  => 'WAPP',
                'name'       => 'Pattimura International Airport',
                'city'       => 'Ambon',
                'province'   => 'Maluku',
                'country'    => 'ID',
                'latitude'   => -3.7103,
                'longitude'  => 128.0882,
                'elevation'  => 33,
                'status'     => true,
            ],
        ];

        foreach ($airports as $airport) {
            Airport::updateOrCreate(
                ['iata_code' => $airport['iata_code']],
                $airport
            );
        }

        $this->command->info('✅ ' . count($airports) . ' bandara demo berhasil di-seed.');
        $this->command->warn('⚠️  Data bandara ini adalah DEMO. Ganti dengan data resmi dari Daftar_Kode_Bandara_Indonesia.xlsx');
    }
}
