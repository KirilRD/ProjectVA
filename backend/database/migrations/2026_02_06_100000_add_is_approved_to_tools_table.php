<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. is_approved mirrors status for quick filtering (pending tools).
     */
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('status');
        });

        DB::table('tools')->where('status', 'approved')->update(['is_approved' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
