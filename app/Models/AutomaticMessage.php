<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Jobs\ProcessCampaignJob;

class AutomaticMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'subject',
        'content',
        'channel',
        'state',
        'scheduled_at',
        'sent_at',
        'ia_response',
        'recipients_count',
        'sent_count',
        'failed_count',
    ];

    protected $casts = [
        'ia_response' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'recipients_count' => 'integer',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    // Métodos útiles
    public function markAsSent(): bool
    {
        return $this->update([
            'state' => 'enviado',
            'sent_at' => now(),
        ]);
    }

    public function canBeSent(): bool
    {
        return in_array($this->state, ['borrador', 'programado'])
            && $this->campaign->clients()->count() > 0;
    }

    /**
     * Enviar mensaje a todos los clientes de la campaña
     */
    public function send(array $customData = []): void
    {
        // Verificar que se puede enviar
        if (!$this->canBeSent()) {
            throw new \Exception('El mensaje no se puede enviar en su estado actual');
        }

        // Despachar job para procesar la campaña
        ProcessCampaignJob::dispatch($this, $customData);

        // Actualizar estado
        $this->update(['state' => 'programado']);
    }

    /**
     * Programar envío
     */
    public function schedule(\DateTime $scheduledAt, array $customData = []): void
    {
        $this->update([
            'scheduled_at' => $scheduledAt,
            'state' => 'programado',
        ]);

        // Despachar job con delay
        ProcessCampaignJob::dispatch($this, $customData)
            ->delay($scheduledAt);
    }
}
