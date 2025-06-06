<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserStatisticsResource\Pages;

use App\Filament\Resources\UserStatisticsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserStatistics extends CreateRecord
{
    protected static string $resource = UserStatisticsResource::class;
}
