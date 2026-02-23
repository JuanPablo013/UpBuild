<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'quotations';

    protected $fillable = [
        'client_id',
        'user_id',
        'code',
        'subtotal',
        'taxes',
        'discount',
        'total',
        'status',
        'issue_date',
        'expiration_date',
        'terms_conditions',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
