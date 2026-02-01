<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use App\Services\AiService;
use BackedEnum;
use Livewire\Attributes\Public;

class ProbarIA extends Page
    implements HasForms
{
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected string $view = 'filament.pages.probar_ia';
    protected static ?string $navigationLabel = 'IA Testing';
    protected static ?string $slug = 'probar-ia';

    public string $message = '';
    public string $response = '';
    public bool $loading = false;

    public static function canAccess(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('message')
                    ->label('Mensaje para la IA')
                    ->required()
                    ->placeholder('Escribe algo...'),
            ]);
    }

    public function send(): void
    {
        $this->validate();

        $this->loading = true;

        try {
            $ai = new AiService();
            $this->response = $ai->chat($this->message);
        } catch (\Exception $e) {
            $this->response = 'Error: ' . $e->getMessage();
        }

        $this->loading = false;
    }

    
}