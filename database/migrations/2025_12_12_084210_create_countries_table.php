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
        Schema::create('country', function (Blueprint $table) {
            $table->char('Code', 3)->primary();
            $table->char('Name', 52)->default('');
            $table->enum('Continent', ['Asia', 'Europe', 'North America', 'Africa', 'Oceania', 'Antarctica', 'South America'])->default('Asia');
            $table->char('Region', 26)->default('');
            $table->decimal('SurfaceArea', 10, 2)->default(0.00);
            $table->smallInteger('IndepYear')->nullable();
            $table->integer('Population')->default(0);
            $table->decimal('LifeExpectancy', 3, 1)->nullable();
            $table->decimal('GNP', 10, 2)->nullable();
            $table->decimal('GNPOld', 10, 2)->nullable();
            $table->char('LocalName', 45)->default('');
            $table->char('GovernmentForm', 45)->default('');
            $table->char('HeadOfState', 60)->nullable();
            $table->integer('Capital')->nullable();
            $table->char('Code2', 2)->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country');
    }
};
