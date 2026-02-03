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
        Schema::create('automatic_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();
            $table->string('subject')->nullable();
            $table->text('content');
            $table->enum('channel', [
                'email',
                'sms',
                'whatsapp',
                'push_notification'
            ])->default('email');
            $table->enum('state', [
                'borrador',
                'programado',
                'enviando',
                'enviado',
                'fallido',
                'cancelado'
            ])->default('borrador');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('ia_response')->nullable();
            $table->integer('recipients_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['campaign_id', 'state']);
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automatic_messages');
    }
};
