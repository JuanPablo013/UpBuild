<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientCampaign extends Model
{
    protected $table = 'client_campaign';

    protected $fillable = [
        'campaign_id',
        'client_id',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos útiles
    |--------------------------------------------------------------------------
    */

    public function markAsSent()
    {
        $this->update([
            'status' => 'enviado',
            'sent_at' => now()
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'entregado',
            'delivered_at' => now()
        ]);
    }

    public function markAsRead()
    {
        $this->update([
            'status' => 'leido',
            'read_at' => now()
        ]);
    }

    public function markAsFailed()
    {
        $this->update([
            'status' => 'fallido'
        ]);
    }
}