<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah semua data status yang ada ke nilai yang sesuai
        DB::statement("ALTER TABLE karyawans MODIFY status VARCHAR(20) DEFAULT 'Permanent'");
        
        // Update data yang ada
        DB::table('karyawans')->where('status', 'aktif')->update(['status' => 'Permanent']);
        DB::table('karyawans')->where('status', 'pending')->update(['status' => 'Contract']);
        DB::table('karyawans')->whereNotIn('status', ['Permanent', 'Contract', 'Outsource'])->update(['status' => 'Permanent']);
        
        // Ubah kolom menjadi enum
        DB::statement("ALTER TABLE karyawans MODIFY status ENUM('Permanent', 'Contract', 'Outsource') DEFAULT 'Permanent'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE karyawans MODIFY status VARCHAR(20) DEFAULT 'Permanent'");
    }
};