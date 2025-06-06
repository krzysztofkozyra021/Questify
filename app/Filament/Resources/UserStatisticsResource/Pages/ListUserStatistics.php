<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserStatisticsResource\Pages;

use App\Filament\Resources\UserStatisticsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserStatistics extends ListRecords
{
    protected static string $resource = UserStatisticsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
