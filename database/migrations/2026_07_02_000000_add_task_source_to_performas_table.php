<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('performas', function (Blueprint $table) {
            $table->string('task_source')->default('manual')->after('task_score');
        });
    }

    public function down(): void
    {
        Schema::table('performas', function (Blueprint $table) {
            $table->dropColumn('task_source');
        });
    }
};
