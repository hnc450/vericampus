<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_field_permissions', function (Blueprint $table) {
            $table->id();
            // company_id = null -> politique globale par défaut, appliquée à toute entreprise
            // sans règle spécifique.
            $table->foreignUlid('company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('field_name');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_field_permissions');
    }
};
