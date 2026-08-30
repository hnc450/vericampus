<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUlid('university_id')->nullable()->after('id')->constrained('universities')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->enum('status', ['active', 'suspended'])->default('active')->after('password');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->text('two_factor_secret')->nullable()->after('last_login_at');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_secret');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('university_id');
            $table->dropColumn(['phone', 'status', 'last_login_at', 'two_factor_secret', 'two_factor_confirmed_at', 'deleted_at']);
        });
    }
};
