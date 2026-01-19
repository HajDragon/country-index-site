<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('country_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('country_id', 3);
            $table->foreign('country_id')->references('Code')->on('country')->cascadeOnDelete();
            $table->enum('interaction_type', ['view', 'favorite', 'compare', 'search']);
            $table->string('session_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes for performance
            $table->index(['country_id', 'created_at']);
            $table->index(['user_id', 'interaction_type']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_interactions');
    }
};
