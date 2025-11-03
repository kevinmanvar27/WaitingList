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
        Schema::table('restaurants', function (Blueprint $table) {
            // Add structured address fields
            $table->string('address_line_1')->nullable()->after('location');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('city')->nullable()->after('address_line_2');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->default('India')->after('state');
            $table->string('postal_code')->nullable()->after('country');

            // Add owner name field (separate from user relationship)
            $table->string('owner_name')->nullable()->after('owner_id');

            // Add description field
            $table->text('description')->nullable()->after('owner_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'country',
                'postal_code',
                'owner_name',
                'description'
            ]);
        });
    }
};
