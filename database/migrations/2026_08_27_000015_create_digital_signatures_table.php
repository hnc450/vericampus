<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('university_id')->constrained()->cascadeOnDelete();
            $table->string('authority_name');
            $table->string('authority_title');
            // Chemin sur le disque privé "signatures" — jamais servi par une route publique.
            $table->string('signature_image_path');
            $table->date('valid_from');
            $table->date('valid_until')->nullable();
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
    }
};
