<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportController extends Controller
{
    public function index()
    {
        return Inertia::render('Support');
    }

    public function feature()
    {
        return Inertia::render('ReportFeature');
    }

    public function bug()
    {
        return Inertia::render('ReportBug');
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // TODO: Implementacja wysyłania wiadomości (np. przez mail)

        return back()->with('success', 'Wiadomość została wysłana.');
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|in:ui,functionality,performance,other',
        ]);

        // TODO: Implementacja zapisywania zgłoszenia funkcji

        return back()->with('success', 'Zgłoszenie funkcji zostało wysłane.');
    }

    public function storeBug(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'steps' => 'required|string',
            'expected' => 'required|string',
            'actual' => 'required|string',
            'browser' => 'nullable|string|max:255',
            'os' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high,critical',
            'screenshots.*' => 'nullable|image|max:10240', // max 10MB
        ]);

        // TODO: Implementacja zapisywania zgłoszenia błędu i obsługi zrzutów ekranu

        return back()->with('success', 'Zgłoszenie błędu zostało wysłane.');
    }
} 