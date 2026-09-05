<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_payment_orders', function (Blueprint $table): void {
            $table->timestamp('discarded_at')->nullable()->after('dismissed_at');
            $table->foreignId('discarded_by')->nullable()->after('discarded_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('construction_payment_orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('discarded_by');
            $table->dropColumn('discarded_at');
        });
    }
};
