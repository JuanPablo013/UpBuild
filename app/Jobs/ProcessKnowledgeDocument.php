<?php
namespace App\Jobs;

use App\Models\KnowledgeDocument;
use App\Services\ChunksDocuments\KnowledgeChunkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;

class ProcessKnowledgeDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public KnowledgeDocument $document
    ) {}

    public function handle(): void
    {
        Log::info('🚀 Job started', [
            'document_id' => $this->document->id,
            'file_path' => $this->document->file_path,
        ]);

        try {
            $relativePath = 'knowledge/' . $this->document->file_path;
            $fullPath = Storage::disk('public')->path($relativePath);

            Log::info('📂 Checking file', [
                'relative_path' => $relativePath,
                'full_path' => $fullPath,
                'exists' => file_exists($fullPath),
            ]);

            if (!file_exists($fullPath)) {
                throw new \Exception("File not found: {$fullPath}");
            }

            $text = match ($this->document->file_type) {
                'pdf' => Pdf::getText($fullPath),
                'txt' => Storage::disk('public')->get($relativePath),
                default => '',
            };

            Log::info('✅ Text extracted', [
                'length' => strlen($text),
                'preview' => substr($text, 0, 100),
            ]);

            $text = trim(preg_replace('/\s+/', ' ', $text));

            // ⭐ CAMBIO CRÍTICO: updateQuietly en lugar de update
            $this->document->updateQuietly([
                'raw_text' => $text,
            ]);

            Log::info('💾 Document updated successfully', [
                'saved_length' => strlen($this->document->fresh()->raw_text ?? ''),
            ]);

            ProcessKnowledgeDocumentChunks::dispatch($this->document);


        } catch (\Exception $e) {
            Log::error('❌ Job failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            throw $e;
        }
    }
}