<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE tools MODIFY roles JSON NULL');
            return;
        }
        // SQLite: change() requires doctrine/dbal; skip so roles stays NOT NULL (app uses empty array)
        if ($driver === 'sqlite') {
            return;
        }
        Schema::table('tools', function (Blueprint $table) {
            $table->json('roles')->nullable()->change();
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE tools MODIFY roles JSON NOT NULL');
            return;
        }
        if ($driver === 'sqlite') {
            return;
        }
        Schema::table('tools', function (Blueprint $table) {
            $table->json('roles')->nullable(false)->change();
        });
    }
};
