<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AutomaticMessage;

class SendScheduledMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar mensajes programados que están listos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $messages = AutomaticMessage::where('state', 'programado')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($messages->isEmpty()) {
            $this->info('No hay mensajes programados para enviar.');
            return self::SUCCESS;
        }

        $this->info("Enviando {$messages->count()} mensajes programados...");

        foreach ($messages as $message) {
            try {
                $message->send();
                $this->info("✓ Mensaje {$message->id} enviado");
            } catch (\Exception $e) {
                $this->error("✗ Error en mensaje {$message->id}: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
