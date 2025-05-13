<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ClassAttribute;
use App\Models\UserStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClassSelectionController extends Controller
{
    public function show()
    {
        $userStats = UserStatistics::where("user_id", Auth::id())->first();

        if ($userStats && $userStats->class !== null) {
            return redirect()->route("dashboard");
        }

        $classes = ClassAttribute::all();

        return Inertia::render("ClassSelection/Show", [
            "classes" => $classes->map(fn($class) => [
                "id" => $class->id,
                "name" => $class->class_name,
                "health_multiplier" => $class->health_multiplier,
                "energy_multiplier" => $class->energy_multiplier,
                "exp_multiplier" => $class->exp_multiplier,
                "special_ability" => $class->special_ability,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "class_id" => "required|exists:class_attributes,id",
        ]);

        $userStats = UserStatistics::where("user_id", Auth::id())->first();

        if ($userStats && $userStats->class !== null) {
            return redirect()->route("dashboard")
                ->with("error", "You have already selected a class and cannot change it.");
        }

        $selectedClass = ClassAttribute::findOrFail($validated["class_id"]);

        if (!$userStats) {
            $userStats = new UserStatistics();
            $userStats->user_id = Auth::id();
        }

        $maxHealth = 100 * $selectedClass->health_multiplier;
        $maxEnergy = 100 * $selectedClass->energy_multiplier;

        $userStats->class = $selectedClass->id;
        $userStats->max_health = $maxHealth;
        $userStats->max_energy = $maxEnergy;
        $userStats->current_health = $maxHealth;
        $userStats->current_energy = $maxEnergy;
        $userStats->current_experience = 0;
        $userStats->next_level_experience = 100;
        $userStats->level = 1;
        $userStats->energy_regen_rate = 1.0;
        $userStats->last_login = now();
        $userStats->last_reset = now();

        $userStats->save();

        return redirect()->route("dashboard")
            ->with("message", "Class selected successfully: " . $selectedClass->class_name);
    }
}
