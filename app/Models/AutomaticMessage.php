<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomaticMessage extends Model
{
    use SoftDeletes;

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
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'ia_response' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
