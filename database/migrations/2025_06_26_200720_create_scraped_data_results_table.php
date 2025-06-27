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
        Schema::create('scraped_data_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scraped_result_id');
            $table->unsignedBigInteger('scraped_data_id');
            $table->foreign('scraped_data_id')->references('id')->on('scraped_data')->onDelete('cascade');
            $table->foreign('scraped_result_id')->references('id')->on('scraped_results')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraped_data_results');
    }
};
