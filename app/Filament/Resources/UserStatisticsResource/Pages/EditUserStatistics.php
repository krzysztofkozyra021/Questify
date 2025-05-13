<?php

namespace App\Filament\Resources\UserStatisticsResource\Pages;

use App\Filament\Resources\UserStatisticsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserStatistics extends EditRecord
{
    protected static string $resource = UserStatisticsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 