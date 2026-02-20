<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeChunk extends Model
{
    use HasFactory;

    protected $table = 'knowledge_chunks';

    protected $fillable = [
        'knowledge_document_id',
        'chunk_index',
        'content',
        'embedding',
    ];

   protected function casts(): array
{
    return [
        'embedding' => 'array',
    ];
}

    /**
     * Relación: un chunk pertenece a un documento de conocimiento
     */
    public function knowledgeDocument()
    {
        return $this->belongsTo(KnowledgeDocument::class);
    }
}
