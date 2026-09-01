<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('security_branches', function (Blueprint $table) {
            $table->text('description')->nullable()->after('code');
            $table->string('country', 100)->default('México')->after('address');
            $table->string('state', 120)->nullable()->after('country');
            $table->string('city', 120)->nullable()->after('state');
            $table->string('postal_code', 20)->nullable()->after('city');
            $table->string('phone', 40)->nullable()->after('postal_code');
            $table->string('email')->nullable()->after('phone');
            $table->string('timezone', 100)->default('America/Mexico_City')->after('email');
            $table->string('status', 20)->default('active')->after('timezone');
            $table->boolean('analytics_enabled')->default(false)->after('status');
            $table->boolean('alerts_enabled')->default(false)->after('analytics_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('security_branches', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'country',
                'state',
                'city',
                'postal_code',
                'phone',
                'email',
                'timezone',
                'status',
                'analytics_enabled',
                'alerts_enabled',
            ]);
        });
    }
};
