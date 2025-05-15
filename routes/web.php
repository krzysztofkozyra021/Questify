<?php

declare(strict_types=1);

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClassSelectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserStatisticsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get("/", fn() => redirect()->route("register"));

Route::inertia("/about", "About");

Route::get("/language/{locale}", [LanguageController::class, "switch"])->name("language.switch");

require __DIR__ . "/auth.php";

// Routes requiring authentication
Route::middleware(["auth"])->group(function (): void {
    // Class selection (post-registration, one-time only)
    Route::get("/select-class", [ClassSelectionController::class, "show"])->name("select-class");
    Route::post("/select-class", [ClassSelectionController::class, "store"])->name("select-class.store");

    // Dashboard - Main game interface
    Route::get("/dashboard", [DashboardController::class, "index"])->middleware(["auth"])->name("dashboard");
    Route::post("/user/health", [DashboardController::class, "updateHealth"])->name("user.health");
    Route::post("/user/addExperience", [DashboardController::class, "addExperience"])->name("user.addExperience");
    Route::get("/dashboard/motivational-quote/{locale}", [DashboardController::class, "getMotivationalQuote"])->name("dashboard.motivational-quote");

    // Tasks - User-specific tasks
    Route::prefix("tasks")->name("tasks.")->group(function (): void {
        Route::get("/create", [DashboardController::class, "create"])->name("create");
        Route::post("/", [DashboardController::class, "store"])->name("store");
        Route::post("/{task}/complete", [DashboardController::class, "completeTask"])->name("complete");
        Route::post("/{task}/reset", [DashboardController::class, "resetTask"])->name("reset");
    });

    // Tags - User-specific tags
    Route::prefix("tags")->name("tags.")->group(function (): void {
        Route::get("/", [TagController::class, "index"])->name("index");
        Route::post("/", [TagController::class, "store"])->name("store");
        Route::get("/{tag}", [TagController::class, "show"])->name("show");
        Route::put("/{tag}", [TagController::class, "update"])->name("update");
        Route::delete("/{tag}", [TagController::class, "destroy"])->name("destroy");
        Route::get("/filter/{tag}", [TagController::class, "filter"])->name("filter");
    });

    // Character & Stats (view only - no class change allowed)
    Route::get("/character", [UserStatisticsController::class, "show"])->name("character");
    Route::get("/character/stats", [UserStatisticsController::class, "stats"])->name("character.stats");
    Route::get("/character/progression", [UserStatisticsController::class, "progression"])->name("character.progression");

    // Calendar - For viewing tasks' due dates
    Route::get("/calendar", [CalendarController::class, "index"])->name("calendar");
    Route::get("/calendar/week", [CalendarController::class, "week"])->name("calendar.week");
    Route::get("/calendar/month", [CalendarController::class, "month"])->name("calendar.month");

    // Profile
    Route::get("/profile", [ProfileController::class, "edit"])->name("profile.edit");
    Route::patch("/profile", [ProfileController::class, "update"])->name("profile.update");
    Route::delete("/profile", [ProfileController::class, "destroy"])->name("profile.destroy");
    Route::post("/profile/image", [ProfileController::class, "updateProfileImage"])->name("profile.image");
    Route::get("/profile/image", [ProfileController::class, "getProfileImage"])->name("profile.image");
    // Settings
    Route::get("/settings", [SettingsController::class, "index"])->name("settings");
    Route::put("/settings", [SettingsController::class, "update"])->name("settings.update");
    Route::post("/settings/locale", [SettingsController::class, "changeLocale"])->name("settings.changeLocale");

    // Support
    Route::get("/support", [SupportController::class, "index"])->name("support");
    Route::post("/support/contact", [SupportController::class, "contact"])->name("support.contact");
});
