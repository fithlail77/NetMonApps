<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['up', 'down', 'warning', 'unknown']);
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['device_id', 'changed_at']);
            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_status_logs');
    }
};
