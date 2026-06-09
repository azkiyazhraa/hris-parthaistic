<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            // Drop old columns if exists
            if (Schema::hasColumn('penggajians', 'total_tunjangan')) {
                $table->dropColumn('total_tunjangan');
            }
            if (Schema::hasColumn('penggajians', 'uang_lembur')) {
                $table->dropColumn('uang_lembur');
            }
            if (Schema::hasColumn('penggajians', 'bonus')) {
                $table->dropColumn('bonus');
            }
            if (Schema::hasColumn('penggajians', 'jumlah_pajak')) {
                $table->dropColumn('jumlah_pajak');
            }
            if (Schema::hasColumn('penggajians', 'potongan_bpjs')) {
                $table->dropColumn('potongan_bpjs');
            }

            // Add new columns for detailed earnings
            $table->decimal('transport_allowance', 15, 2)->default(0)->after('gaji_pokok');
            $table->decimal('meal_allowance', 15, 2)->default(0)->after('transport_allowance');
            $table->decimal('internet_allowance', 15, 2)->default(0)->after('meal_allowance');
            $table->decimal('position_allowance', 15, 2)->default(0)->after('internet_allowance');
            $table->decimal('incentive', 15, 2)->default(0)->after('position_allowance');
            $table->decimal('total_earnings', 15, 2)->default(0)->after('incentive');
            
            // Add new columns for deductions
            $table->decimal('tax', 15, 2)->default(0)->after('total_earnings');
            $table->decimal('bpjs_kesehatan', 15, 2)->default(0)->after('tax');
            $table->decimal('bpjs_ketenagakerjaan', 15, 2)->default(0)->after('bpjs_kesehatan');
            $table->decimal('late_absent_deduction', 15, 2)->default(0)->after('bpjs_ketenagakerjaan');
            $table->decimal('loan_deduction', 15, 2)->default(0)->after('late_absent_deduction');
            $table->decimal('total_deductions', 15, 2)->default(0)->after('loan_deduction');
            
            // Add net salary after deductions
            $table->decimal('net_salary', 15, 2)->default(0)->after('total_deductions');
            
            // Add fields for payslip
            $table->timestamp('payslip_sent_at')->nullable()->after('status');
            $table->string('payslip_sent_by', 255)->nullable()->after('payslip_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            $table->dropColumn([
                'transport_allowance',
                'meal_allowance',
                'internet_allowance',
                'position_allowance',
                'incentive',
                'total_earnings',
                'tax',
                'bpjs_kesehatan',
                'bpjs_ketenagakerjaan',
                'late_absent_deduction',
                'loan_deduction',
                'total_deductions',
                'net_salary',
                'payslip_sent_at',
                'payslip_sent_by'
            ]);
            
            // Re-add old columns
            $table->string('total_tunjangan', 50)->default('0')->after('gaji_pokok');
            $table->string('uang_lembur', 50)->default('0')->after('total_tunjangan');
            $table->string('bonus', 50)->default('0')->after('uang_lembur');
            $table->string('jumlah_pajak', 50)->default('0')->after('total_potongan');
            $table->string('potongan_bpjs', 50)->default('0')->after('jumlah_pajak');
        });
    }
};