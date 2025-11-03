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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Plan Name
            $table->integer('duration_days'); // Duration in Days
            $table->decimal('price', 10, 2); // Price with 2 decimal places
            $table->boolean('is_enabled')->default(true); // Enable/Disable Toggle
            $table->text('description')->nullable(); // Optional description
            $table->json('features')->nullable(); // JSON array of features included
            $table->integer('sort_order')->default(0); // For ordering plans
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
