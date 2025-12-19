<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudioResource\Pages;
use App\Filament\Admin\Resources\StudioResource\RelationManagers;
use App\Models\Studio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudioResource extends Resource
{
    protected static ?string $model = Studio::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Event/Resource Management';

    protected static ?string $modelLabel = 'Event/Resource';

    protected static ?string $pluralModelLabel = 'Event/Resources';


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
                Forms\Components\TextInput::make('hourly_rate')
                    ->required()
                    ->numeric()
                    ->prefix(\App\Models\Setting::get('currency_symbol', '₹'))
                    ->suffix(\App\Models\Setting::get('currency_code', 'INR'))
                    ->step(50),
                Forms\Components\TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
                Forms\Components\TagsInput::make('amenities')
                    ->placeholder('Add amenities (e.g., Wi-Fi, Projector, Whiteboard)')
                    ->helperText('Press Enter to add each amenity'),
                
                Forms\Components\Section::make('Scheduling Configuration')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('slot_duration_value')
                                    ->label('Slot Duration')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->slot_duration) {
                                            // Determine if it's hours or minutes
                                            if ($record->slot_duration >= 60 && $record->slot_duration % 60 === 0) {
                                                $component->state($record->slot_duration / 60);
                                            } else {
                                                $component->state($record->slot_duration);
                                            }
                                        }
                                    }),
                                Forms\Components\Select::make('slot_duration_unit')
                                    ->label('Unit')
                                    ->options([
                                        'minutes' => 'Minutes',
                                        'hours' => 'Hours',
                                    ])
                                    ->required()
                                    ->default('hours')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->slot_duration) {
                                            if ($record->slot_duration >= 60 && $record->slot_duration % 60 === 0) {
                                                $component->state('hours');
                                            } else {
                                                $component->state('minutes');
                                            }
                                        }
                                    }),
                            ]),
                        Forms\Components\Hidden::make('slot_duration')
                            ->dehydrateStateUsing(function ($state, $get) {
                                $value = $get('slot_duration_value');
                                $unit = $get('slot_duration_unit');
                                
                                if ($unit === 'hours') {
                                    return $value * 60;
                                }
                                return $value;
                            }),

                        Forms\Components\Repeater::make('allowed_durations')
                            ->label('Booking Duration Options')
                            ->helperText('Define the duration options available for users to select. If empty, the default Slot Duration will be used.')
                            ->schema([
                                Forms\Components\TextInput::make('duration')
                                    ->label('Duration (Minutes)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),
                                Forms\Components\TextInput::make('label')
                                    ->label('Display Label')
                                    ->placeholder('e.g., 1 Hour, 2 Hours')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                        
                        Forms\Components\Repeater::make('operating_hours')
                            ->label('Weekly Schedule')
                            ->schema([
                                Forms\Components\Placeholder::make('day_label')
                                    ->label('Day')
                                    ->content(fn ($state, $get) => new \Illuminate\Support\HtmlString('<strong>' . ucfirst($get('day')) . '</strong>')),
                                Forms\Components\Hidden::make('day'),
                                Forms\Components\TimePicker::make('start')
                                    ->required()
                                    ->default('09:00'),
                                Forms\Components\TimePicker::make('end')
                                    ->required()
                                    ->default('22:00'),
                                Forms\Components\Toggle::make('enabled')
                                    ->default(true),
                            ])
                            ->default([
                                ['day' => 'monday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                ['day' => 'tuesday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                ['day' => 'wednesday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                ['day' => 'thursday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                ['day' => 'friday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                ['day' => 'saturday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                ['day' => 'sunday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                            ])
                            ->afterStateHydrated(function (Forms\Components\Repeater $component, $state) {
                                if (empty($state)) {
                                    $component->state([
                                        ['day' => 'monday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                        ['day' => 'tuesday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                        ['day' => 'wednesday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                        ['day' => 'thursday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                        ['day' => 'friday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                        ['day' => 'saturday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                        ['day' => 'sunday', 'start' => '09:00', 'end' => '22:00', 'enabled' => true],
                                    ]);
                                }
                            })
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columns(4)
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => ucfirst($state['day'] ?? '')),
                    ])
                    ->collapsible(),

                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('studios')
                    ->imageEditor()
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('gallery')
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->panelLayout('grid')
                    ->image()
                    ->directory('studios/gallery')
                    ->maxFiles(5)
                    ->imageEditor()
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hourly_rate')
                    ->money(\App\Models\Setting::get('currency_code', 'INR'))
                    ->suffix(' ' . \App\Models\Setting::get('currency_code', 'INR'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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
            'index' => Pages\ListStudios::route('/'),
            'create' => Pages\CreateStudio::route('/create'),
            'edit' => Pages\EditStudio::route('/{record}/edit'),
        ];
    }
}
