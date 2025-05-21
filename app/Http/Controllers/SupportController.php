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

        // TODO: Implement send message (email)

        return back()->with('success', 'Message sent.');
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|in:ui,functionality,performance,other',
        ]);

        // TODO: Implement save feature report

        return back()->with('success', 'Feature report sent.');
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

        // TODO: Implement save bug report and handle screenshots

        return back()->with('success', 'Bug report sent.');
    }
} 