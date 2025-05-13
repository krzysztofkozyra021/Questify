<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserStatisticsResource\Pages;
use App\Models\UserStatistics;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserStatisticsResource extends Resource
{
    protected static ?string $model = UserStatistics::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('class')
                    ->relationship('classAttributes', 'class_name')
                    ->required(),
                Forms\Components\TextInput::make('current_health')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('current_energy')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('max_health')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('max_energy')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('current_experience')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('next_level_experience')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('level')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('energy_regen_rate')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('classAttributes.class_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_health')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_energy')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_health')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_energy')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_experience')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_level_experience')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('energy_regen_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_reset')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUserStatistics::route('/'),
            'create' => Pages\CreateUserStatistics::route('/create'),
            'edit' => Pages\EditUserStatistics::route('/{record}/edit'),
        ];
    }
}