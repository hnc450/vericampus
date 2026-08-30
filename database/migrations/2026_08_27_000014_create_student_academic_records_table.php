<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('promotion_id')->constrained()->restrictOnDelete();
            $table->foreignId('degree_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('palmares_import_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mention')->nullable();
            $table->decimal('average', 5, 2)->nullable();
            $table->enum('status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->date('graduation_date')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_academic_records');
    }
};
