<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'target_segment',
        'status',
        'start_date',
        'final_date',
        'ia_configuration',
    ];

    protected $casts = [
        'target_segment' => 'array',
        'ia_configuration' => 'array',
        'start_date' => 'date',
        'final_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_campaign')
            ->withPivot([
                'status',
                'sent_at',
                'delivered_at',
                'read_at',
                'metadata'
            ])
            ->withTimestamps();
    }

    // public function automaticMessages(): HasMany
    // {
    //     return $this->hasMany(AutomaticMessage::class, 'campaign_id');
    // }

    // Métodos útiles
    public function canBeActivated(): bool
    {
        return in_array($this->status, ['borrador', 'programada', 'pausada'])
            && $this->clients()->count() > 0;
    }

    public function activate(): bool
    {
        if (!$this->canBeActivated()) {
            return false;
        }

        return $this->update(['status' => 'activa']);
    }
}
