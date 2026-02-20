<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::ensureVectorExtensionExists();

        Schema::table('knowledge_chunks', function (Blueprint $table) {
            $table->vector('embedding', dimensions: 2560)->after('content')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_chunks', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });
    }
};
