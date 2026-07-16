<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained()->onDelete('cascade');
            $table->string('queue_name');
            $table->string('target')->nullable();
            $table->unsignedBigInteger('rx_byte')->default(0);
            $table->unsignedBigInteger('tx_byte')->default(0);
            $table->unsignedBigInteger('rx_rate')->default(0);
            $table->unsignedBigInteger('tx_rate')->default(0);
            $table->timestamps();

            $table->index(['router_id', 'queue_name', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_usage_logs');
    }
};
