<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $karyawanData = [
            // Admin
            [
                'nip'                    => 'ADMIN001',
                'email'                  => 'admin@hris.com',
                'kata_sandi'             => Hash::make('password123'),
                'nama_lengkap'           => 'Budi Santoso',
                'role'                   => 'admin',
                'status'                 => 'Permanent',
                'nomor_telepon'          => '081200000001',
                'jenis_kelamin'          => 'L',
                'agama'                  => 'Islam',
                'tempat_lahir'           => 'Jakarta',
                'tanggal_lahir'          => '1985-03-15',
                'status_pernikahan'      => 'Menikah',
                'pendidikan_terakhir'    => 'S1',
                'universitas'            => 'Universitas Indonesia',
                'jurusan'                => 'Teknik Informatika',
                'tahun_lulus'            => 2007,
                'alamat'                 => 'Jl. Sudirman No. 1, Jakarta Pusat',
                'nik'                    => '3171010000000001',
                'tanggal_bergabung'      => '2020-01-01',
                'nama_kontak_darurat'    => 'Siti Santoso',
                'telepon_kontak_darurat' => '081200000011',
            ],

            // HR
            [
                'nip'                    => 'HR001',
                'email'                  => 'hr@hris.com',
                'kata_sandi'             => Hash::make('password123'),
                'nama_lengkap'           => 'Dewi Rahayu',
                'role'                   => 'hr',
                'status'                 => 'Permanent',
                'nomor_telepon'          => '081200000002',
                'jenis_kelamin'          => 'P',
                'agama'                  => 'Islam',
                'tempat_lahir'           => 'Bandung',
                'tanggal_lahir'          => '1990-07-22',
                'status_pernikahan'      => 'Menikah',
                'pendidikan_terakhir'    => 'S1',
                'universitas'            => 'Universitas Padjadjaran',
                'jurusan'                => 'Psikologi',
                'tahun_lulus'            => 2012,
                'alamat'                 => 'Jl. Asia Afrika No. 10, Bandung',
                'nik'                    => '3273010000000002',
                'tanggal_bergabung'      => '2021-03-01',
                'nama_kontak_darurat'    => 'Ahmad Rahayu',
                'telepon_kontak_darurat' => '081200000012',
            ],

            // Karyawan 1 — Permanent
            [
                'nip'                    => 'EMP0001',
                'email'                  => 'azkiya@hris.com',
                'kata_sandi'             => Hash::make('password123'),
                'nama_lengkap'           => 'Azkiya Zahra',
                'role'                   => 'karyawan',
                'status'                 => 'Permanent',
                'nomor_telepon'          => '081200000003',
                'jenis_kelamin'          => 'P',
                'agama'                  => 'Islam',
                'tempat_lahir'           => 'Yogyakarta',
                'tanggal_lahir'          => '1995-04-10',
                'status_pernikahan'      => 'Belum Menikah',
                'pendidikan_terakhir'    => 'S1',
                'universitas'            => 'Universitas Gadjah Mada',
                'jurusan'                => 'Akuntansi',
                'tahun_lulus'            => 2017,
                'alamat'                 => 'Jl. Malioboro No. 5, Yogyakarta',
                'nik'                    => '3404010000000003',
                'tanggal_bergabung'      => '2022-01-10',
                'nama_kontak_darurat'    => 'Zahra Sari',
                'telepon_kontak_darurat' => '081200000013',
            ],

            // Karyawan 2 — Permanent
            [
                'nip'                    => 'EMP0002',
                'email'                  => 'rizky@hris.com',
                'kata_sandi'             => Hash::make('password123'),
                'nama_lengkap'           => 'Rizky Firmansyah',
                'role'                   => 'karyawan',
                'status'                 => 'Permanent',
                'nomor_telepon'          => '081200000004',
                'jenis_kelamin'          => 'L',
                'agama'                  => 'Islam',
                'tempat_lahir'           => 'Surabaya',
                'tanggal_lahir'          => '1993-09-18',
                'status_pernikahan'      => 'Menikah',
                'pendidikan_terakhir'    => 'S1',
                'universitas'            => 'Institut Teknologi Sepuluh Nopember',
                'jurusan'                => 'Teknik Elektro',
                'tahun_lulus'            => 2015,
                'alamat'                 => 'Jl. Pemuda No. 30, Surabaya',
                'nik'                    => '3578010000000004',
                'tanggal_bergabung'      => '2021-07-01',
                'nama_kontak_darurat'    => 'Rina Firmansyah',
                'telepon_kontak_darurat' => '081200000014',
            ],

            // Karyawan 3 — Contract
            [
                'nip'                    => 'EMP0003',
                'email'                  => 'ani@hris.com',
                'kata_sandi'             => Hash::make('password123'),
                'nama_lengkap'           => 'Ani Rahmawati',
                'role'                   => 'karyawan',
                'status'                 => 'Contract',
                'nomor_telepon'          => '081200000005',
                'jenis_kelamin'          => 'P',
                'agama'                  => 'Islam',
                'tempat_lahir'           => 'Semarang',
                'tanggal_lahir'          => '1997-12-05',
                'status_pernikahan'      => 'Belum Menikah',
                'pendidikan_terakhir'    => 'D3',
                'universitas'            => 'Universitas Diponegoro',
                'jurusan'                => 'Manajemen',
                'tahun_lulus'            => 2019,
                'alamat'                 => 'Jl. Pandanaran No. 15, Semarang',
                'nik'                    => '3374010000000005',
                'tanggal_bergabung'      => '2023-02-01',
                'nama_kontak_darurat'    => 'Hadi Rahmawati',
                'telepon_kontak_darurat' => '081200000015',
            ],

            // Karyawan 4 — Contract
            [
                'nip'                    => 'EMP0004',
                'email'                  => 'dimas@hris.com',
                'kata_sandi'             => Hash::make('password123'),
                'nama_lengkap'           => 'Dimas Prasetyo',
                'role'                   => 'karyawan',
                'status'                 => 'Contract',
                'nomor_telepon'          => '081200000006',
                'jenis_kelamin'          => 'L',
                'agama'                  => 'Kristen',
                'tempat_lahir'           => 'Medan',
                'tanggal_lahir'          => '1996-06-25',
                'status_pernikahan'      => 'Belum Menikah',
                'pendidikan_terakhir'    => 'S1',
                'universitas'            => 'Universitas Sumatera Utara',
                'jurusan'                => 'Sistem Informasi',
                'tahun_lulus'            => 2018,
                'alamat'                 => 'Jl. Gatot Subroto No. 8, Medan',
                'nik'                    => '1271010000000006',
                'tanggal_bergabung'      => '2023-08-15',
                'nama_kontak_darurat'    => 'Linda Prasetyo',
                'telepon_kontak_darurat' => '081200000016',
            ],

            // Karyawan 5 — Outsource
            [
                'nip'                    => 'EMP0005',
                'email'                  => 'sari@hris.com',
                'kata_sandi'             => Hash::make('password123'),
                'nama_lengkap'           => 'Sari Indah Lestari',
                'role'                   => 'karyawan',
                'status'                 => 'Outsource',
                'nomor_telepon'          => '081200000007',
                'jenis_kelamin'          => 'P',
                'agama'                  => 'Islam',
                'tempat_lahir'           => 'Makassar',
                'tanggal_lahir'          => '1998-02-14',
                'status_pernikahan'      => 'Belum Menikah',
                'pendidikan_terakhir'    => 'S1',
                'universitas'            => 'Universitas Hasanuddin',
                'jurusan'                => 'Komunikasi',
                'tahun_lulus'            => 2020,
                'alamat'                 => 'Jl. Penghibur No. 22, Makassar',
                'nik'                    => '7371010000000007',
                'tanggal_bergabung'      => '2024-01-02',
                'nama_kontak_darurat'    => 'Dedi Lestari',
                'telepon_kontak_darurat' => '081200000017',
            ],
        ];

        foreach ($karyawanData as $data) {
            Karyawan::create($data);
        }
    }
}
