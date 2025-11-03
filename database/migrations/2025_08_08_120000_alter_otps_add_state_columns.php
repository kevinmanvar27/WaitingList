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
        Schema::table('otps', function (Blueprint $table) {
            // Track OTP lifecycle and resends
            $table->string('status')->default('generated')->after('otp'); // generated|sent|used|expired
            $table->timestamp('expires_at')->nullable()->after('created_at');
            $table->timestamp('used_at')->nullable()->after('expires_at');
            $table->timestamp('last_sent_at')->nullable()->after('used_at');

            // Helpful index for lookups
            $table->index(['email', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->dropIndex(['email', 'status', 'created_at']);
            $table->dropColumn(['status', 'expires_at', 'used_at', 'last_sent_at']);
        });
    }
};

