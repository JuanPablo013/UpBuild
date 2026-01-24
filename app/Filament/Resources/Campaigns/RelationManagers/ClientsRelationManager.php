<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DetachAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\RelationManagers\RelationManager;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';

    protected static ?string $modelLabel = 'Clientes de la Campaña';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_type')
                    ->label('Tipo de Cliente')
                    ->options([
                        'persona_natural' => 'Persona Natural',
                        'empresa' => 'Empresa',
                        'pyme' => 'PYME',
                        'corporativo' => 'Corporativo',
                    ])
                    ->required()
                    ->native(false)
                    ->default('persona_natural'),

                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),

                TextInput::make('telephone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(255),

                Select::make('funnel_stage')
                    ->label('Etapa del Embudo')
                    ->options([
                        'prospecto' => 'Prospecto',
                        'contactado' => 'Contactado',
                        'calificado' => 'Calificado',
                        'propuesta' => 'Propuesta',
                        'negociacion' => 'Negociación',
                        'cerrado_ganado' => 'Cerrado Ganado',
                        'cerrado_perdido' => 'Cerrado Perdido',
                    ])
                    ->required()
                    ->native(false),

                Select::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente',
                    ])
                    ->required()
                    ->native(false)
                    ->default('media'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon(Heroicon::Envelope)
                    ->copyable(),

                TextColumn::make('telephone')
                    ->label('Teléfono')
                    ->searchable()
                    ->icon(Heroicon::Phone),

                TextColumn::make('funnel_stage')
                    ->label('Etapa')
                    ->badge()
                    ->colors([
                        'gray' => 'prospecto',
                        'info' => 'contactado',
                        'primary' => 'calificado',
                        'warning' => 'propuesta',
                        'secondary' => 'negociacion',
                        'success' => 'cerrado_ganado',
                        'danger' => 'cerrado_perdido',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        ucfirst(str_replace('_', ' ', $state))
                    ),

                // Columnas de la tabla pivote
                TextColumn::make('pivot.status')
                    ->label('Estado en Campaña')
                    ->badge()
                    ->colors([
                        'gray' => 'pendiente',
                        'info' => 'enviado',
                        'success' => 'entregado',
                        'primary' => 'leido',
                        'warning' => 'respondido',
                        'danger' => 'fallido',
                    ])
                    ->formatStateUsing(
                        fn(string $state): string =>
                        ucfirst($state)
                    ),

                TextColumn::make('pivot.sent_at')
                    ->label('Enviado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('pivot.delivered_at')
                    ->label('Entregado')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pivot.read_at')
                    ->label('Leído')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('funnel_stage')
                    ->label('Etapa')
                    ->options([
                        'prospecto' => 'Prospecto',
                        'contactado' => 'Contactado',
                        'calificado' => 'Calificado',
                        'propuesta' => 'Propuesta',
                        'negociacion' => 'Negociación',
                        'cerrado_ganado' => 'Cerrado Ganado',
                        'cerrado_perdido' => 'Cerrado Perdido',
                    ]),

                SelectFilter::make('pivot.status')
                    ->label('Estado en Campaña')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'enviado' => 'Enviado',
                        'entregado' => 'Entregado',
                        'leido' => 'Leído',
                        'respondido' => 'Respondido',
                        'fallido' => 'Fallido',
                    ]),
            ])
            ->headerActions([
                // Attach existente
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->schema(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('status')
                            ->label('Estado Inicial')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'enviado' => 'Enviado',
                                'entregado' => 'Entregado',
                                'leido' => 'Leído',
                                'respondido' => 'Respondido',
                                'fallido' => 'Fallido',
                            ])
                            ->default('pendiente')
                            ->required(),
                    ]),

                // Crear nuevo cliente y agregarlo
                CreateAction::make()
                    ->label('Crear Cliente'),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->orderBy('client_campaign.created_at', 'desc');
            });
    }
}
