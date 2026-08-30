<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('palmares_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('palmares_import_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->json('raw_data');
            $table->enum('status', ['imported', 'rejected', 'duplicate']);
            $table->foreignUlid('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('palmares_rows');
    }
};
