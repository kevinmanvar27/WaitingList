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
            // Add restaurant_id to properly associate users with specific restaurants
            $table->unsignedBigInteger('restaurant_id')->nullable()->after('added_by');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            
            // Add index for better performance
            $table->index(['restaurant_id', 'status', 'created_at']);
        });
        
        // Populate restaurant_id for existing records
        // For now, assign users to the first restaurant of their owner
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            // SQLite does not support table aliases in UPDATE, so use PHP
            $users = DB::table('restaurant_users')->whereNull('restaurant_id')->get();
            foreach ($users as $user) {
                $restaurant = DB::table('restaurants')
                    ->where('owner_id', $user->added_by)
                    ->orderBy('id', 'asc')
                    ->first();
                if ($restaurant) {
                    DB::table('restaurant_users')
                        ->where('id', $user->id)
                        ->update(['restaurant_id' => $restaurant->id]);
                }
            }
        } else {
            // MySQL/Postgres: use raw SQL
            DB::statement("
                UPDATE restaurant_users
                SET restaurant_id = (
                    SELECT r.id 
                    FROM restaurants r 
                    WHERE r.owner_id = restaurant_users.added_by 
                    ORDER BY r.id ASC 
                    LIMIT 1
                )
                WHERE restaurant_id IS NULL
            ");
        }
        
        // Make restaurant_id required after populating existing data
        Schema::table('restaurant_users', function (Blueprint $table) {
            $table->unsignedBigInteger('restaurant_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_users', function (Blueprint $table) {
            $table->dropForeign(['restaurant_id']);
            $table->dropIndex(['restaurant_id', 'status', 'created_at']);
            $table->dropColumn('restaurant_id');
        });
    }
};
