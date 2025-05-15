<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TagController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tags = Tag::whereHas('tasks', function ($query) use ($user) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        })->get();

        return Inertia::render('Tags/Index', [
            'tags' => $tags
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name'
        ]);

        $tag = Tag::create($validated);

        return back();
    }

    public function show(Tag $tag)
    {
        $user = auth()->user();
        $tasks = $tag->tasks()
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with(['difficulty', 'resetConfig'])
            ->get();

        return Inertia::render('Tags/Show', [
            'tag' => $tag,
            'tasks' => $tasks
        ]);
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id
        ]);

        $tag->update($validated);

        return back();
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return back();
    }

    public function filter(Tag $tag)
    {
        $user = auth()->user();
        $tasks = $tag->tasks()
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with(['difficulty', 'resetConfig'])
            ->get();

        return response()->json([
            'tasks' => $tasks
        ]);
    }
} 