<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('university_id')->constrained()->cascadeOnDelete();
            $table->foreignId('faculty_id')->constrained()->restrictOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_number');
            $table->string('unique_student_id')->unique();
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('first_name');
            $table->enum('sex', ['M', 'F'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('status', ['active', 'graduated', 'dropped', 'suspended'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['university_id', 'student_number']);
            $table->index(['university_id', 'last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
