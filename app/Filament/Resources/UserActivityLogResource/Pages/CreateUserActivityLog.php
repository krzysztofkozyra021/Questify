<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserActivityLogResource\Pages;

use App\Filament\Resources\UserActivityLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserActivityLog extends CreateRecord
{
    protected static string $resource = UserActivityLogResource::class;
}
