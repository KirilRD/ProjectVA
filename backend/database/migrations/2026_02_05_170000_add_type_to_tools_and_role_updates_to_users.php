<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds type to tools table and ensures role exists on users table.
     */
    public function up(): void
    {
        // SQLite does not support AFTER in ALTER TABLE; columns are added at end
        if (Schema::hasTable('tools') && ! Schema::hasColumn('tools', 'type')) {
            Schema::table('tools', function (Blueprint $table) {
                $table->string('type', 50)->default('tool');
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 50)->default('frontend');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tools') && Schema::hasColumn('tools', 'type')) {
            Schema::table('tools', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
