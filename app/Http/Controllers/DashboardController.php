<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskDifficulty;
use App\Models\TaskResetConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the logged-in user
        $user = auth()->user();

        // Get user's tasks with appropriate relations
        $tasks = $user->tasks()
            ->with(['tags', 'difficulty', 'resetConfig'])
            ->orderBy('created_at', 'desc')
            ->get();

        $userStatistics = $user->userStatistics;

        // Pass data to the view
        return Inertia::render('Dashboard', [
            'tasks' => $tasks,
            'userStatistics' => $userStatistics,
            'user' => $user,
        ]);
    }

    public function create()
    {
        // Get available difficulty levels and reset configurations for the form
        $difficulties = TaskDifficulty::orderBy('difficulty_level')->get();
        $resetConfigs = TaskResetConfig::all();

        return Inertia::render('Tasks/Create', [
            'difficulties' => $difficulties,
            'resetConfigs' => $resetConfigs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty_level' => 'required|integer|exists:difficulties,id',
            'reset_frequency' => 'required|integer|exists:reset_configs,id',
            'start_date' => 'required|date',
            'due_date' => 'nullable|date|after:start_date',
            'repeat_every' => 'required|integer|min:1',
            'repeat_unit' => 'required|in:day,week,month',
            'is_completed' => 'boolean',
            'is_deadline_task' => 'boolean',
            'experience_reward' => 'required|integer|min:1',
            'tags' => 'array',
            'tags.*' => 'string|max:50',
        ]);

        // Assign task to the logged-in user
        $task = auth()->user()->tasks()->create($validated);

        if (!empty($validated['tags'])) {
            $task->tags()->createMany(
                collect($validated['tags'])->map(fn($tag) => ['name' => $tag])->toArray()
            );
        }

        return redirect()->route('dashboard');
    }

    public function updateHealth(Request $request)
    {
        $user = auth()->user();
        $userStats = $user->userStatistics;
        
        // Reduce health by 10 points, but not below 0
        $newHealth = max(0, $userStats->current_health - 10);
        $userStats->current_health = round($newHealth);
        $userStats->max_health = round($userStats->max_health);
        $userStats->save();

        return back()->with('userStatistics', $userStats);
    }

    public function levelUp(Request $request)
    {
        $user = auth()->user();
        $userStats = $user->userStatistics;

        $userStats->current_experience = $userStats->next_level_experience;
        $userStats->save();

        return back()->with('userStatistics', $userStats);
    }

    

    public function getMotivationalQuote($locale)
{
    if (env('ENABLE_APIS', false)) return;

    // Set default language for Forismatic API
    $apiLang = 'en';
    
    // If locale is Russian, use it directly with Forismatic
    if (strtolower(substr($locale, 0, 2)) === 'ru') {
        $apiLang = 'ru';
    }
    
    // Call Forismatic API
    $response = Http::asForm()->post('http://api.forismatic.com/api/1.0/', [
        'method' => 'getQuote',
        'format' => 'json',
        'lang' => $apiLang
    ]);
    
    if ($response->status() !== 200) {
        return response()->json([
            'error' => 'Failed to fetch quote from Forismatic API'
        ], 500);
    }
    
    $quote = $response->json();
    
    $quoteText = $quote['quoteText'];
    $quoteAuthor = $quote['quoteAuthor'] ?: 'Unknown'; 
    
    $locale = strtoupper($locale);
    
    // If the requested language is the same as API language, no translation needed
    if ((strtolower(substr($locale, 0, 2)) === 'en' && $apiLang === 'en') || 
        (strtolower(substr($locale, 0, 2)) === 'ru' && $apiLang === 'ru')) {
        return response()->json([
            [
                'q' => $quoteText,
                'a' => $quoteAuthor
            ]
        ]);
    }
    
    $translated_quote = $this->translateWithDeepl($quoteText, $locale);
    
    return response()->json([
        [
            'q' => $translated_quote['translations'][0]['text'],
            'a' => $quoteAuthor
        ]
    ]);
}

    public function translateWithDeepl($text, $target_lang){

        if (env('ENABLE_APIS', false)) return;
        $deepl_api_key = env('DEEPL_API_KEY');
        $deepl_translated_quote = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'DeepL-Auth-Key ' . $deepl_api_key,
        ])->post('https://api-free.deepl.com/v2/translate', [
            'text' => [$text],
            'target_lang' => $target_lang,
        ]);

        if ($deepl_translated_quote->status() !== 200) {
            return response()->json([
                'error' => 'Failed to translate quote from ZenQuotes API'
            ], 500);
        }
        return $deepl_translated_quote->json();
    }
    
}
