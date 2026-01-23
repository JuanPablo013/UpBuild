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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_type');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('department')->nullable();
            $table->decimal('estimated_budget', 12, 2)->nullable();
            $table->enum('funnel_stage', [
                'prospecto',
                'contactado',
                'calificado',
                'propuesta',
                'negociacion',
                'cerrado_ganado',
                'cerrado_perdido'
            ])->default('prospecto');
            $table->integer('ia_score')->default(0);
            $table->enum('priority', ['baja', 'media', 'alta', 'urgente'])
                ->default('media');
            $table->json('additional_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para optimizar búsquedas
            $table->index(['funnel_stage']);
            $table->index(['priority']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
