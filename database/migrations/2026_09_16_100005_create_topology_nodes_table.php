<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topology_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->float('x_position')->default(0);
            $table->float('y_position')->default(0);
            $table->string('label')->nullable();
            $table->timestamps();

            $table->unique(['device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topology_nodes');
    }
};
