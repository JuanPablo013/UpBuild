<?php

namespace App\Jobs;

use App\Mail\CampaignEmail;
use App\Models\AutomaticMessage;
use App\Models\Client;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendCampaignEmailJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public int $backoff = 30; // Segundos entre reintentos

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AutomaticMessage $message,
        public Client $client,
        public array $customData = []
    ) {
        // Configurar cola según prioridad
        $this->onQueue($client->priority === 'urgent' ? 'high' : 'emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Verificar si el batch fue cancelado
        if ($this->batch()?->cancelled()) {
            return;
        }

        try {
            // Enviar email
            Mail::to($this->client->email)
                ->send(new CampaignEmail(
                    $this->message,
                    $this->client,
                    $this->customData
                ));

            // Actualizar pivot con estado de envío
            $this->client->campaigns()->updateExistingPivot(
                $this->message->campaign_id,
                [
                    'status' => 'enviado',
                    'sent_at' => now(),
                ]
            );

            // Incrementar contador de enviados
            $this->message->increment('sent_count');

            // Log de éxito
            Log::info('Email enviado correctamente', [
                'message_id' => $this->message->id,
                'client_id' => $this->client->id,
                'email' => $this->client->email,
            ]);

            // Rate limiting para evitar errores de Mailtrap/SendGrid en pruebas
            // if (app()->environment('local', 'testing')) {
            //     sleep(1); // Esperar 1 segundo entre envíos
            // }
        } catch (Throwable $e) {
            // Incrementar contador de fallidos
            $this->message->increment('failed_count');

            // Actualizar pivot con estado de fallo
            $this->client->campaigns()->updateExistingPivot(
                $this->message->campaign_id,
                [
                    'status' => 'fallido',
                    'metadata' => [
                        'error' => $e->getMessage(),
                        'failed_at' => now()->toISOString(),
                    ],
                ]
            );

            // Log de error
            Log::error('Error enviando email', [
                'message_id' => $this->message->id,
                'client_id' => $this->client->id,
                'email' => $this->client->email,
                'error' => $e->getMessage(),
            ]);

            // Re-lanzar excepción para que el job sea marcado como fallido
            throw $e;
        } finally {
            // Verificar si la campaña terminó
            $this->checkIfCampaignFinished();
        }
    }

    /**
     * Verificar si la campaña ha terminado
     */
    private function checkIfCampaignFinished(): void
    {
        try {
            // Recargar modelo para obtener contadores frescos
            $this->message->refresh();

            $totalProcessed = $this->message->sent_count + $this->message->failed_count;

            if ($totalProcessed >= $this->message->recipients_count) {
                // Evitar actualizar si ya está marcado como terminado
                if ($this->message->state !== 'enviado') {
                    $this->message->update([
                        'state' => 'enviado',
                        'sent_at' => now(),
                    ]);

                    Log::info('Campaña completada (verificada desde job)', [
                        'message_id' => $this->message->id,
                        'total' => $totalProcessed
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error verificando fin de campaña', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        // Marcar mensaje como fallido después de todos los intentos
        if ($this->attempts() >= $this->tries) {
            $this->message->update(['state' => 'fallido']);
        }

        Log::error('Job de email falló definitivamente', [
            'message_id' => $this->message->id,
            'client_id' => $this->client->id,
            'attempts' => $this->attempts(),
            'exception' => $exception?->getMessage(),
        ]);
    }
}
