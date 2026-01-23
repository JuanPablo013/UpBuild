<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    // 🔑 Primary key personalizada
    protected $primaryKey = 'idCompany';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'nit',
        'website',
        'phone',
        'custom_fields',
    ];

    // 👇 Esto es lo que faltaba
    protected $casts = [
        'custom_fields' => 'array', // o 'json'
    ];
}
