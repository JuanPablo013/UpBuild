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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', [
                'email',
                'sms',
                'whatsapp',
                'redes_sociales',
                'mixta'
            ])->default('email');
            $table->json('target_segment')->nullable();
            $table->enum('status', [
                'borrador',
                'programada',
                'activa',
                'pausada',
                'completada',
                'cancelada'
            ])->default('borrador');
            $table->date('start_date')->nullable();
            $table->date('final_date')->nullable();
            $table->json('ia_configuration')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['status']);
            $table->index(['start_date', 'final_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
