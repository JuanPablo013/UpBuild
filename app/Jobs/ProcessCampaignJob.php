<?php

namespace App\Jobs;

use App\Models\AutomaticMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class ProcessCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AutomaticMessage $message,
        public array $customData = []
    ) {
        $this->onQueue('high');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Obtener clientes de la campaña
        $clients = $this->message->campaign->clients()
            ->whereNotNull('email')
            ->get();

        if ($clients->isEmpty()) {
            Log::warning('No hay clientes con email en la campaña', [
                'campaign_id' => $this->message->campaign_id,
                'message_id' => $this->message->id,
            ]);
            return;
        }

        // Actualizar contadores
        $this->message->update([
            'recipients_count' => $clients->count(),
            'state' => 'enviando',
        ]);

        // Crear batch de jobs
        $jobs = $clients->map(function ($client) {
            return new SendCampaignEmailJob(
                $this->message,
                $client,
                $this->customData
            );
        });

        // Despachar batch (sin callbacks para evitar errores de serialización)
        $batch = Bus::batch($jobs)
            ->name("Campaign {$this->message->campaign->name} - Message {$this->message->id}")
            ->allowFailures()
            ->onQueue('emails')
            ->dispatch();

        Log::info('Batch de campaña creado', [
            'message_id' => $this->message->id,
            'batch_id' => $batch->id,
            'jobs_count' => $clients->count(),
        ]);

        // Guardar batch ID en el mensaje
        $this->message->update([
            'ia_response' => array_merge(
                $this->message->ia_response ?? [],
                ['batch_id' => $batch->id]
            ),
        ]);
    }
}
