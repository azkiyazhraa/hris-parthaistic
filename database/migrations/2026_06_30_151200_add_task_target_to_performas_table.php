<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('performas', function (Blueprint $table) {
            $table->integer('task_target')->default(0)->after('task_done');
        });
    }

    public function down(): void
    {
        Schema::table('performas', function (Blueprint $table) {
            $table->dropColumn('task_target');
        });
    }
};
