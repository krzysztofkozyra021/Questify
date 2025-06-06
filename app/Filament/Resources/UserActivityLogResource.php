<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserActivityLogResource\Pages;
use App\Models\UserActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserActivityLogResource extends Resource
{
    protected static ?string $model = UserActivityLog::class;
    protected static ?string $navigationIcon = "heroicon-o-rectangle-stack";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make("user_id")
                    ->relationship("user", "name")
                    ->required(),
                Forms\Components\TextInput::make("activity_type")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make("ip_address")
                    ->maxLength(255),
                Forms\Components\TextInput::make("user_agent")
                    ->maxLength(255),
                Forms\Components\TextInput::make("additional_data"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("user.name")
                    ->sortable(),
                Tables\Columns\TextColumn::make("activity_type")
                    ->searchable(),
                Tables\Columns\TextColumn::make("ip_address")
                    ->searchable(),
                Tables\Columns\TextColumn::make("user_agent")
                    ->searchable(),
                Tables\Columns\TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListUserActivityLogs::route("/"),
            "create" => Pages\CreateUserActivityLog::route("/create"),
            "edit" => Pages\EditUserActivityLog::route("/{record}/edit"),
        ];
    }
}
