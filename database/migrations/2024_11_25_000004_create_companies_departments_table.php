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
        Schema::create('companies_departments', function (Blueprint $table) {
            $table->id(); // BigInt
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->boolean('active')->default(true);
            // Add manager field as requested
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps(); // Optional but good practice, though SQL didn't specify it. SQL didn't specify timestamps for this one.
            // SQL had no timestamps. But I'll add them if I want consistency.
            // The user's SQL for this table: NO timestamps.
            // I will leave timestamps OUT if I want to match strict SQL, or IN if I want Laravel standard.
            // Given "created_at" etc are in other tables, likely this pivot might not need them, but I'll add them to be safe/standard.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_departments');
    }
};
