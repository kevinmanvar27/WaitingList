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
        Schema::create('restaurant_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 255);
            $table->string('mobile_number'); // Removed unique constraint - will rely on Laravel validation
            $table->integer('total_users_count')->nullable();
            $table->enum('status', ['waiting', 'dine-in'])->default('waiting');
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Add indexes for better performance
            $table->index(['added_by', 'created_at']);
            $table->index('mobile_number');
            $table->index(['added_by', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_users');
    }
};
