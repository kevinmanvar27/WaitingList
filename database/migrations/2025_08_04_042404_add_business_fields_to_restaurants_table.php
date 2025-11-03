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
            $table->string('operating_hours')->nullable()->after('description');
            $table->string('cuisine_type')->nullable()->after('operating_hours');
            $table->string('website')->nullable()->after('cuisine_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['operating_hours', 'cuisine_type', 'website']);
        });
    }
};
