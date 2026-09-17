<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topology_edges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_node_id')->constrained('topology_nodes')->cascadeOnDelete();
            $table->foreignId('target_node_id')->constrained('topology_nodes')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->enum('status', ['up', 'down', 'warning', 'unknown'])->default('unknown');
            $table->timestamps();

            $table->index(['source_node_id', 'target_node_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topology_edges');
    }
};
