<?php

namespace App\Observers;

use App\Jobs\ProcessKnowledgeDocument;
use App\Models\KnowledgeDocument;

class KnowledgeDocumentObserver
{
    /**
     * Handle the KnowledgeDocument "created" event.
     */
    public function created(KnowledgeDocument $knowledgeDocument): void
    {
         //ProcessKnowledgeDocumentument::dispatch($document);
    }

    /**
     * Handle the KnowledgeDocument "updated" event.
     */
    public function updated(KnowledgeDocument $knowledgeDocument): void
    {
        //
    }

    /**
     * Handle the KnowledgeDocument "deleted" event.
     */
    public function deleted(KnowledgeDocument $knowledgeDocument): void
    {
        //
    }

    /**
     * Handle the KnowledgeDocument "restored" event.
     */
    public function restored(KnowledgeDocument $knowledgeDocument): void
    {
        //
    }

    /**
     * Handle the KnowledgeDocument "force deleted" event.
     */
    public function forceDeleted(KnowledgeDocument $knowledgeDocument): void
    {
        //
    }
}
