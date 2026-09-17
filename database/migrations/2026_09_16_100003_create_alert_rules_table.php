<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->string('metric_type');
            $table->enum('condition', ['greater_than', 'less_than', 'equals']);
            $table->float('threshold');
            $table->enum('severity', ['critical', 'warning', 'info'])->default('warning');
            $table->boolean('is_active')->default(true);
            $table->boolean('notify_email')->default(true);
            $table->boolean('notify_telegram')->default(false);
            $table->timestamps();

            $table->index(['device_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_rules');
    }
};
