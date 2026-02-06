<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->string('official_docs_link')->nullable()->after('link');
            $table->text('usage_instructions')->nullable()->after('how_to_use');
            $table->string('examples_link')->nullable()->after('examples');
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn(['official_docs_link', 'usage_instructions', 'examples_link']);
        });
    }
};
