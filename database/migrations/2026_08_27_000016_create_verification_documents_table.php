<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_documents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            // Identifiant public affiché sur le document (ex: en pied de page).
            $table->uuid('document_uid')->unique();
            // Jeton opaque encodé dans le QR Code — distinct de document_uid pour ne jamais
            // exposer directement l'identifiant primaire ou l'UID imprimé sur le document.
            $table->string('qr_token', 64)->unique();
            $table->foreignUlid('student_id')->constrained()->restrictOnDelete();
            $table->foreignUlid('university_id')->constrained()->restrictOnDelete();
            $table->foreignId('faculty_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('degree_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('signature_id')->nullable()->constrained('digital_signatures')->nullOnDelete();
            $table->foreignUlid('generated_by_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('generated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['verified', 'revoked', 'expired'])->default('verified');
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoked_reason')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_documents');
    }
};
