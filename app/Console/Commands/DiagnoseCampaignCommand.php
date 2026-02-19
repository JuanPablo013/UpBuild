<?php

namespace App\Console\Commands;

use App\Models\AutomaticMessage;
use Illuminate\Console\Command;

class DiagnoseCampaignCommand extends Command
{
    protected $signature = 'campaigns:diagnose {message-id}';
    protected $description = 'Diagnosticar problemas con el envío de campañas';

    public function handle()
    {
        $messageId = $this->argument('message-id');
        $message = AutomaticMessage::with('campaign.clients')->find($messageId);

        if (!$message) {
            $this->error("Mensaje {$messageId} no encontrado");
            return self::FAILURE;
        }

        $this->info("=== Diagnóstico del Mensaje {$messageId} ===");
        $this->newLine();

        // Info básica
        $this->line("📧 Asunto: {$message->subject}");
        $this->line("📊 Estado: {$message->state}");
        $this->line("🏷️  Campaña ID: {$message->campaign_id}");
        $this->line("📅 Programado: " . ($message->scheduled_at ?? 'No programado'));
        $this->newLine();

        // Campaña
        if (!$message->campaign) {
            $this->error("❌ La campaña no existe");
            return self::FAILURE;
        }

        $this->line("📢 Campaña: {$message->campaign->name}");
        $this->line("🎯 Tipo: {$message->campaign->type}");
        $this->newLine();

        // Clientes
        $clientsCount = $message->campaign->clients()->count();
        $clientsWithEmail = $message->campaign->clients()->whereNotNull('email')->count();

        $this->line("👥 Total clientes: {$clientsCount}");
        $this->line("📧 Clientes con email: {$clientsWithEmail}");
        $this->newLine();

        if ($clientsCount === 0) {
            $this->error("❌ La campaña no tiene clientes asociados");
            return self::FAILURE;
        }

        if ($clientsWithEmail === 0) {
            $this->error("❌ Ningún cliente tiene email");
            return self::FAILURE;
        }

        // Listar clientes
        $this->line("📋 Clientes:");
        $message->campaign->clients()->each(function ($client) {
            $email = $client->email ?? '(sin email)';
            $this->line("  - {$client->name} <{$email}>");
        });
        $this->newLine();

        // Verificar si puede enviarse
        $canBeSent = $message->canBeSent();
        if ($canBeSent) {
            $this->info("✅ El mensaje PUEDE enviarse");
        } else {
            $this->error("❌ El mensaje NO puede enviarse");
            $this->line("   Estado debe ser 'borrador' o 'programado'");
            $this->line("   Estado actual: {$message->state}");
        }

        $this->newLine();
        $this->line("📊 Contadores:");
        $this->line("  - Destinatarios: {$message->recipients_count}");
        $this->line("  - Enviados: {$message->sent_count}");
        $this->line("  - Fallidos: {$message->failed_count}");

        return self::SUCCESS;
    }
}
