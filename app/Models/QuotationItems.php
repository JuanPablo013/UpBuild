<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItems extends Model
{
    protected $table = 'quotations_items';

    protected $fillable = [
        'quotation_id',
        'concept',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'subtotal',
        'order',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }
}
