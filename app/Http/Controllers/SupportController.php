<?php

namespace App\Http\Controllers;

use App\Mail\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

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

        try {
            \Log::info('Attempting to send email with data:', $validated);
            \Log::info('Data types:', array_map('gettype', $validated));
            Mail::to('support@questify.com')->send(new Contact($validated));
            \Log::info('Email sent successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to send message. Please try again later.');
        }

        return back()->with('success', 'Message sent successfully.');
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