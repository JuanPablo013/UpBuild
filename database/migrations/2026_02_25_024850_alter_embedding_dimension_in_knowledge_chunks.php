<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('knowledge_chunks', function (Blueprint $table) {
             DB::statement('ALTER TABLE knowledge_chunks ALTER COLUMN embedding TYPE vector(1536)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_chunks', function (Blueprint $table) {
            DB::statement('ALTER TABLE knowledge_chunks ALTER COLUMN embedding TYPE vector(2560)');
        });
    }
};
