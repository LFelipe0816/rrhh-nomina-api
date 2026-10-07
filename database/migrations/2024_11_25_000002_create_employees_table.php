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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 191);
            $table->string('lastname', 191);
            $table->string('email', 191)->unique();
            $table->string('phone', 191)->nullable();
            $table->string('job_position', 191);
            $table->string('address', 191)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('state', 191)->nullable();
            $table->string('postal_code', 191)->nullable();
            $table->string('country', 191)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('hire_date')->nullable()->index();
            $table->decimal('monthly_salary', 12, 2)->default(0.00);
            $table->string('employment_status', 191)->default('active')->index();
            $table->string('emergency_contact_name', 191)->nullable();
            $table->string('emergency_contact_phone', 191)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
