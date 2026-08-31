<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'university_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignUlid('university_id')->nullable()->after('id')->constrained('universities')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable()->after('email');
            });
        }

        if (! Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('status', ['active', 'suspended'])->default('active')->after('password');
            });
        }

        if (! Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable()->after('status');
            });
        }

        if (! Schema::hasColumn('users', 'two_factor_secret')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('two_factor_secret')->nullable()->after('last_login_at');
            });
        }

        if (! Schema::hasColumn('users', 'two_factor_confirmed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_secret');
            });
        }

        if (! Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'university_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('university_id');
            });
        }

        $columns = array_filter([
            Schema::hasColumn('users', 'phone') ? 'phone' : null,
            Schema::hasColumn('users', 'status') ? 'status' : null,
            Schema::hasColumn('users', 'last_login_at') ? 'last_login_at' : null,
            Schema::hasColumn('users', 'two_factor_secret') ? 'two_factor_secret' : null,
            Schema::hasColumn('users', 'two_factor_confirmed_at') ? 'two_factor_confirmed_at' : null,
            Schema::hasColumn('users', 'deleted_at') ? 'deleted_at' : null,
        ]);

        if (! empty($columns)) {
            Schema::table('users', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
