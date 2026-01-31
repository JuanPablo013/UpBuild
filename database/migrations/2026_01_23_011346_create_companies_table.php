<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id('idCompany')->primary();
            $table->string('name');
            $table->string('nit')->nullable()->index();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->json('custom_fields')->nullable();
            $table->softDeletes();
            $table->index(['name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
