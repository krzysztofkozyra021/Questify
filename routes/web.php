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

require __DIR__ . "/auth.php";

Route::get("/", function () {
    if (!auth()->check()) { return inertia("Auth/Register"); }
    return redirect()->route("dashboard");
})->name("home");

Route::get("/language/{locale}", [LanguageController::class, "switch"])->name("language.switch");

// Information pages
Route::inertia("/about", "Support/About")->name("about");
Route::inertia("/faq", "Support/Faq")->name("faq");
Route::inertia("/terms", "Support/Terms")->name("terms");
Route::inertia("/policy", "Support/Policy")->name("policy");
Route::inertia("/contact", "Support/Contact")->name("contact");

// Support and reports
Route::get("/support", [SupportController::class, "index"])->name("support");
Route::post("/support/contact", [SupportController::class, "contact"])->name("support.contact");
Route::get("/report/feature", [SupportController::class, "feature"])->name("report.feature");
Route::post("/report/feature", [SupportController::class, "sendFeatureReport"])->name("report.feature.send");
Route::get("/report/bug", [SupportController::class, "bug"])->name("report.bug");
Route::post("/report/bug", [SupportController::class, "sendBugReport"])->name("report.bug.send");

// Routes requiring authentication
Route::middleware(["auth"])->group(function (): void {
    // Class selection (post-registration, one-time only)
    Route::get("/select-class", [ClassSelectionController::class, "show"])->name("select-class");
    Route::post("/select-class", [ClassSelectionController::class, "store"])->name("select-class.store");

    // Dashboard - Main game interface
    Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");
    Route::get("/motivational-quote/{locale}", [DashboardController::class, "getMotivationalQuote"])->name("dashboard.getMotivationalQuote");
    Route::get("/api/user/dashboard-data", [DashboardController::class, "getDashboardData"])->name("api.user.dashboard-data");

    // Tasks - User-specific tasks
    Route::prefix("tasks")->name("tasks.")->group(function (): void {
        // Habits routes
        Route::prefix("habits")->name("habits.")->group(function (): void {
            Route::post("/store", [TaskController::class, "storeHabit"])->name("store");
            Route::put("/update/{habit}", [TaskController::class, "updateHabit"])->name("update");
            Route::delete("/{habit}", [TaskController::class, "destroyHabit"])->name("destroy");
            Route::post("/{habit}/complete", [TaskController::class, "completeHabit"])->name("complete");
            Route::post("/{habit}/not-completed", [TaskController::class, "habitNotCompleted"])->name("not-completed");
        });

        // Todos routes
        Route::prefix("todos")->name("todos.")->group(function (): void {
            Route::post("/store", [TaskController::class, "storeTodo"])->name("store");
            Route::put("/update/{todo}", [TaskController::class, "updateTodo"])->name("update");
            Route::post("/{todo}/complete", [TaskController::class, "completeTodo"])->name("complete");
            Route::delete("/{todo}", [TaskController::class, "destroyTodo"])->name("destroy");
        });

        // Dailies routes
        Route::prefix("dailies")->name("dailies.")->group(function (): void {
            Route::post("/store", [TaskController::class, "storeDaily"])->name("store");
            Route::put("/update/{task}", [TaskController::class, "updateDaily"])->name("update");
            Route::post("/{task}/complete", [TaskController::class, "completeDaily"])->name("complete");
        });

        // Common task routes
        Route::post("/{task}/reset", [TaskController::class, "resetTask"])->name("reset");
        Route::put("/{task}", [TaskController::class, "update"])->name("update");
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
});
