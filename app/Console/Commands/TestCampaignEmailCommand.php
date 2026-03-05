<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCampaignJob;
use App\Jobs\SendCampaignEmailJob;
use App\Mail\CampaignEmail;
use App\Models\AutomaticMessage;
use App\Models\Campaign;
use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestCampaignEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:test-email 
                            {--email= : Email de destino para la prueba}
                            {--message-id= : ID del mensaje automático a probar}
                            {--campaign-id= : ID de la campaña a probar}
                            {--mode=single : Modo de prueba (single|job|full)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el envío de emails de campaña con SendGrid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mode = $this->option('mode');

        $this->info("🧪 Iniciando prueba de emails en modo: {$mode}");
        $this->newLine();

        try {
            match ($mode) {
                'single' => $this->testSingleEmail(),
                'job' => $this->testWithJob(),
                'full' => $this->testFullCampaign(),
                default => $this->error("Modo inválido. Usa: single, job, o full")
            };
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            $this->error("Stack trace: {$e->getTraceAsString()}");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Prueba envío directo de un email
     */
    private function testSingleEmail(): void
    {
        $email = $this->option('email') ?? $this->ask('Email de destino');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email inválido');
            return;
        }

        // Obtener o crear datos de prueba
        $message = $this->getTestMessage();
        $client = $this->getTestClient($email);

        $this->info("📧 Enviando email de prueba a: {$email}");
        $this->info("📝 Asunto: {$message->subject}");
        $this->newLine();

        // Enviar email directamente
        Mail::to($email)->send(new CampaignEmail(
            $message,
            $client,
            [
                'cta_text' => 'Ver más',
                'cta_url' => 'https://ejemplo.com'
            ]
        ));

        $this->info("✅ Email enviado correctamente!");
        $this->displayVerificationSteps();
    }

    /**
     * Prueba con Job (sin batch)
     */
    private function testWithJob(): void
    {
        $email = $this->option('email') ?? $this->ask('Email de destino');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email inválido');
            return;
        }

        $message = $this->getTestMessage();
        $client = $this->getTestClient($email);

        $this->info("🚀 Despachando Job de envío de email...");
        $this->newLine();

        // Despachar job
        SendCampaignEmailJob::dispatch($message, $client, [
            'cta_text' => 'Ver más',
            'cta_url' => 'https://ejemplo.com'
        ]);

        $this->info("✅ Job despachado correctamente!");
        $this->warn("⏳ El email se enviará cuando se procese la cola.");
        $this->newLine();
        $this->comment("Ejecuta: php artisan queue:work");
        $this->displayVerificationSteps();
    }

    /**
     * Prueba campaña completa
     */
    private function testFullCampaign(): void
    {
        $messageId = $this->option('message-id');

        if (!$messageId) {
            $this->error('Debes proporcionar --message-id para probar una campaña completa');
            return;
        }

        $message = AutomaticMessage::find($messageId);

        if (!$message) {
            $this->error("No se encontró el mensaje con ID: {$messageId}");
            return;
        }

        $clientsCount = $message->campaign->clients()->count();

        if ($clientsCount === 0) {
            $this->error("La campaña no tiene clientes asociados");
            return;
        }

        $this->info("📊 Campaña: {$message->campaign->name}");
        $this->info("📧 Clientes: {$clientsCount}");
        $this->info("📝 Asunto: {$message->subject}");
        $this->newLine();

        if (!$this->confirm('¿Deseas procesar esta campaña completa?', false)) {
            $this->warn('Operación cancelada');
            return;
        }

        // Despachar job de procesamiento
        ProcessCampaignJob::dispatch($message, [
            'cta_text' => 'Ver más',
            'cta_url' => 'https://ejemplo.com'
        ]);

        $this->info("✅ Campaña despachada correctamente!");
        $this->warn("⏳ Los emails se enviarán cuando se procese la cola.");
        $this->newLine();
        $this->comment("Ejecuta: php artisan queue:work");
        $this->displayVerificationSteps();
    }

    /**
     * Obtener mensaje de prueba
     */
    private function getTestMessage(): AutomaticMessage
    {
        $messageId = $this->option('message-id');

        if ($messageId) {
            $message = AutomaticMessage::find($messageId);
            if ($message) {
                return $message;
            }
        }

        // Buscar primer mensaje disponible
        $message = AutomaticMessage::with('campaign')->first();

        if (!$message) {
            throw new \Exception('No hay mensajes automáticos en la base de datos');
        }

        return $message;
    }

    /**
     * Obtener cliente de prueba
     */
    private function getTestClient(string $email): Client
    {
        // Buscar cliente existente con ese email
        $client = Client::where('email', $email)->first();

        if ($client) {
            return $client;
        }

        // Crear cliente temporal de prueba
        $campaign = $this->getTestMessage()->campaign;

        $client = new Client([
            'name' => 'Usuario de Prueba',
            'email' => $email,
            'telephone' => '+1234567890',
            'city' => 'Ciudad de Prueba',
        ]);

        // No guardar en BD, solo usar en memoria
        $client->id = 99999;
        $client->exists = true;

        // Simular relación con campaña
        $client->setRelation('campaigns', collect([$campaign]));

        return $client;
    }

    /**
     * Mostrar pasos de verificación
     */
    private function displayVerificationSteps(): void
    {
        $this->newLine();
        $this->info("📋 Pasos de verificación:");
        $this->line("1. Revisa tu bandeja de entrada");
        $this->line("2. Verifica el dashboard de SendGrid: https://app.sendgrid.com/");
        $this->line("3. Revisa los logs: storage/logs/laravel.log");
        $this->line("4. Si usaste jobs, verifica: php artisan queue:failed");
    }
}
