<?php

namespace App\Providers;

use App\Models\KnowledgeDocument;
use Illuminate\Support\ServiceProvider;
use App\Observers\KnowledgeDocumentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        KnowledgeDocument::observe(KnowledgeDocumentObserver::class);
    }
}
