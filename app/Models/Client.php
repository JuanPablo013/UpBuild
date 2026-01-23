<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_type',
        'name',
        'email',
        'telephone',
        'address',
        'city',
        'department',
        'estimated_budget',
        'funnel_stage',
        'ia_score',
        'priority',
        'additional_data',
    ];

    protected $casts = [
        'estimated_budget' => 'decimal:2',
        'ia_score' => 'integer',
        'additional_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'client_campaign')
            ->withPivot([
                'status',
                'sent_at',
                'delivered_at',
                'read_at',
                'metadata'
            ])
            ->withTimestamps();
    }
}
