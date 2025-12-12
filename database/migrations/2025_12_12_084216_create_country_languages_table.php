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
        Schema::create('countrylanguage', function (Blueprint $table) {
            $table->char('CountryCode', 3)->default('');
            $table->char('Language', 30)->default('');
            $table->enum('IsOfficial', ['T', 'F'])->default('F');
            $table->decimal('Percentage', 4, 1)->default(0.0);

            $table->primary(['CountryCode', 'Language']);
            $table->index('CountryCode');
            $table->foreign('CountryCode')->references('Code')->on('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countrylanguage');
    }
};
