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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'scraping', 'security', 'system', 'user'
            $table->string('action'); // 'completed', 'detected', 'updated', 'created', etc.
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('info'); // 'success', 'warning', 'error', 'info'
            $table->string('icon')->nullable(); // icon class or name
            $table->json('metadata')->nullable(); // additional data as JSON
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('related_type')->nullable(); // model class name
            $table->unsignedBigInteger('related_id')->nullable(); // model id
            $table->timestamp('occurred_at');
            $table->timestamps();
            
            $table->index(['type', 'occurred_at']);
            $table->index(['status', 'occurred_at']);
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
