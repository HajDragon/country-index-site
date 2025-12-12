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
        Schema::create('city', function (Blueprint $table) {
            $table->integer('ID')->primary()->autoIncrement();
            $table->char('Name', 35)->default('');
            $table->char('CountryCode', 3)->default('');
            $table->char('District', 20)->default('');
            $table->integer('Population')->default(0);

            $table->index('CountryCode');
            $table->foreign('CountryCode')->references('Code')->on('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city');
    }
};
