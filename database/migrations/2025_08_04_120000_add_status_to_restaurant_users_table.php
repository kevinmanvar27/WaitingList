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
        Schema::table('restaurant_users', function (Blueprint $table) {
            // Add status field if it doesn't exist
            if (!Schema::hasColumn('restaurant_users', 'status')) {
                $table->enum('status', ['waiting', 'dine-in'])->default('waiting')->after('total_users_count');
                
                // Add index for better performance on status queries
                $table->index(['added_by', 'status', 'created_at']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_users', function (Blueprint $table) {
            if (Schema::hasColumn('restaurant_users', 'status')) {
                // Drop the index first
                $table->dropIndex(['added_by', 'status', 'created_at']);
                // Drop the status column
                $table->dropColumn('status');
            }
        });
    }
};
