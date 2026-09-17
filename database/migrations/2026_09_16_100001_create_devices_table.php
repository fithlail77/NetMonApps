<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address');
            $table->foreignId('device_type_id')->constrained()->cascadeOnDelete();
            $table->string('snmp_community')->nullable();
            $table->enum('snmp_version', ['v1', 'v2c', 'v3'])->default('v2c');
            $table->integer('snmp_port')->default(161);
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['up', 'down', 'warning', 'unknown'])->default('unknown');
            $table->timestamp('last_seen_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['ip_address']);
            $table->index('status');
            $table->index('device_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
