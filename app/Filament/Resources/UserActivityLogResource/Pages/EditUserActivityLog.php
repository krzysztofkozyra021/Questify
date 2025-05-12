<?php

namespace App\Filament\Resources\UserActivityLogResource\Pages;

use App\Filament\Resources\UserActivityLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserActivityLog extends EditRecord
{
    protected static string $resource = UserActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
