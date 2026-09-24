<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('security_companies', function (Blueprint $table) {
            $table->boolean('is_favorite')->default(false);
        });

        // Keep the companies already visible in the carousel visible after this upgrade.
        DB::table('security_companies')->update(['is_favorite' => true]);

        Schema::create('security_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_company_id')->constrained('security_companies')->cascadeOnDelete();
            $table->foreignId('security_branch_id')->nullable()->constrained('security_branches')->nullOnDelete();
            $table->foreignId('security_camera_id')->nullable()->constrained('security_cameras')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->string('priority', 16)->default('medium');
            $table->string('status', 16)->default('pending');
            $table->dateTime('occurred_at');
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['security_company_id', 'occurred_at']);
            $table->index(['status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_incidents');

        Schema::table('security_companies', function (Blueprint $table) {
            $table->dropColumn('is_favorite');
        });
    }
};
