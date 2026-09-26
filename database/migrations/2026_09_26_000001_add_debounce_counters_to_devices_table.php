<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->unsignedTinyInteger('consecutive_failures')->default(0)->after('status');
            $table->unsignedTinyInteger('consecutive_successes')->default(0)->after('consecutive_failures');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['consecutive_failures', 'consecutive_successes']);
        });
    }
};
