<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('occasional_service_payments', function (Blueprint $table) {
            $table->date('payment_paid_on')->nullable()->index();
            $table->string('payment_file_path')->nullable();
            $table->string('payment_original_name')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
        });

        $fuelCategoryId = DB::table('occasional_services')->where('name', 'Vale de Gasolina')->value('id');
        if ($fuelCategoryId) {
            DB::table('occasional_service_entries')->updateOrInsert(
                ['occasional_service_id' => $fuelCategoryId, 'reference_number' => 'EFECTIVALE-CONCENTRADORA'],
                ['name' => 'Efectivale', 'updated_at' => now(), 'created_at' => now()],
            );
        }
    }

    public function down(): void
    {
        Schema::table('occasional_service_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paid_by');
            $table->dropColumn(['payment_paid_on', 'payment_file_path', 'payment_original_name']);
        });

        $entry = DB::table('occasional_service_entries')->where('reference_number', 'EFECTIVALE-CONCENTRADORA')->first();
        if ($entry
            && ! DB::table('occasional_service_payments')->where('occasional_service_entry_id', $entry->id)->exists()
            && ! DB::table('occasional_service_payment_allocations')->where('occasional_service_entry_id', $entry->id)->exists()) {
            DB::table('occasional_service_entries')->where('id', $entry->id)->delete();
        }
    }
};
