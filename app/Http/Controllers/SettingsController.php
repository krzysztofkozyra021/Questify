<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): Response
    {
        $user = Auth::user()->load(['userStatistics', 'userStatistics.classAttributes']);
        $userStatistics = $user->userStatistics;
        
        return Inertia::render('Settings', [
            'user' => $user,
            'userStatistics' => $userStatistics,
        ]);
    }

    /**
     * Update the user's settings.
     */
    public function update(SettingsUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Update user settings
        $user->update($request->validated());

        return redirect()->route('settings')->with('success', 'Settings updated successfully.');
    }
} 