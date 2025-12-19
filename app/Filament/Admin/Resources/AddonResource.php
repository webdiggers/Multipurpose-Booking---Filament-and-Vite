<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AddonResource\Pages;
use App\Filament\Admin\Resources\AddonResource\RelationManagers;
use App\Models\Addon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddonResource extends Resource
{
    protected static ?string $model = Addon::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationGroup = 'Event/Resource Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->maxLength(65535),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix(\App\Models\Setting::get('currency_symbol', '₹'))
                    ->suffix(\App\Models\Setting::get('currency_code', 'INR'))
                    ->step(10),
                Forms\Components\Select::make('type')
                    ->options([
                        'hourly_extension' => 'Hourly Extension',
                        'equipment' => 'Equipment',
                        'service' => 'Service',
                        'other' => 'Other',
                    ])
                    ->required()
                    ->default('other'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money(\App\Models\Setting::get('currency_code', 'INR'))
                    ->suffix(' ' . \App\Models\Setting::get('currency_code', 'INR'))
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'hourly_extension',
                        'success' => 'equipment',
                        'warning' => 'service',
                        'secondary' => 'other',
                    ]),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'hourly_extension' => 'Hourly Extension',
                        'equipment' => 'Equipment',
                        'service' => 'Service',
                        'other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAddons::route('/'),
            'create' => Pages\CreateAddon::route('/create'),
            'edit' => Pages\EditAddon::route('/{record}/edit'),
        ];
    }
}
