<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Cache;


Route::middleware(["auth:sanctum"])->group(function (): void {
    Route::get("/user", fn(Request $request): JsonResponse => new JsonResponse($request->user()));

    Route::get('/overdue-days-sync', function () {
        $syncDetails = Cache::get('overdue_sync_details');
        
        if (!$syncDetails) {
            return response()->json([
                'last_sync_timestamp' => 0,
                'last_sync_date' => null,
                'has_synced' => false,
                'affected_tasks' => 0,
            ]);
        }
    });
});
