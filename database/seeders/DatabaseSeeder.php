<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        Karyawan::create([
            'nip' => 'ADMIN001',
            'email' => 'admin@hris.com',
            'kata_sandi' => Hash::make('password123'),
            'nama_lengkap' => 'Administrator HRIS',
            'role' => 'admin',
            'status' => 'Permanent',
            'tanggal_bergabung' => now(),
        ]);

        // // HR
        // Karyawan::create([
        //     'nip' => 'HR001',
        //     'email' => 'hr@hris.com',
        //     'kata_sandi' => Hash::make('password123'),
        //     'nama_lengkap' => 'HR Manager',
        //     'role' => 'hr',
        //     'status' => 'Permanent',
        //     'tanggal_bergabung' => now(),
        // ]);

        // Karyawan Permanent
        Karyawan::create([
            'nip' => 'EMP001',
            'email' => 'azkiya@gmail.com',
            'kata_sandi' => Hash::make('password123'),
            'nama_lengkap' => 'Azkiya Zahra',
            'role' => 'karyawan',
            'status' => 'Permanent',
            'tanggal_bergabung' => now(),
        ]);
        

        // // Karyawan Contract
        // Karyawan::create([
        //     'nip' => 'EMP002',
        //     'email' => 'karyawan2@hris.com',
        //     'kata_sandi' => Hash::make('password123'),
        //     'nama_lengkap' => 'Ani Rahmawati',
        //     'role' => 'karyawan',
        //     'status' => 'Contract',
        //     'tanggal_bergabung' => now(),
        // ]);

        // // Karyawan Outsource
        // Karyawan::create([
        //     'nip' => 'EMP003',
        //     'email' => 'karyawan3@hris.com',
        //     'kata_sandi' => Hash::make('password123'),
        //     'nama_lengkap' => 'Charlie Putra',
        //     'role' => 'karyawan',
        //     'status' => 'Outsource',
        //     'tanggal_bergabung' => now(),
        // ]);
    }
}
