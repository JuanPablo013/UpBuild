<?php

namespace App\Filament\Resources\AutomaticMessages;

use App\Filament\Resources\AutomaticMessages\Pages\CreateAutomaticMessage;
use App\Filament\Resources\AutomaticMessages\Pages\EditAutomaticMessage;
use App\Filament\Resources\AutomaticMessages\Pages\ListAutomaticMessages;
use App\Filament\Resources\AutomaticMessages\Schemas\AutomaticMessageForm;
use App\Filament\Resources\AutomaticMessages\Tables\AutomaticMessagesTable;
use App\Models\AutomaticMessage;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AutomaticMessageResource extends Resource
{
    protected static ?string $model = AutomaticMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Envelope;

    protected static string | UnitEnum | null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Mensajes Automáticos';

    protected static ?string $tenantOwnershipRelationshipName = null;

    public static function form(Schema $schema): Schema
    {
        return AutomaticMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AutomaticMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAutomaticMessages::route('/'),
            'create' => CreateAutomaticMessage::route('/create'),
            'edit' => EditAutomaticMessage::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('state', 'programado')->count();
    }
}
