<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeDocument extends Model
{
    use HasFactory;

    protected $table = 'knowledge_documents';

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'raw_text',
    ];

    /**
     * Casts de atributos (opcional, pero útil)
     */
    protected $casts = [
        'raw_text' => 'string',
    ];

      protected $dispatchesEvents = [];
}
