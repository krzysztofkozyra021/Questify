<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;

class TagService
{
    public function getUserTags(User $user): Collection
    {
        return Tag::whereHas('tasks', function ($query) use ($user) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        })->get();
    }

    public function getTasksByTag(Tag $tag, User $user): Collection
    {
        return $tag->tasks()
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with(['difficulty', 'resetConfig'])
            ->get();
    }

    public function createTag(array $data): Tag
    {
        return Tag::create($data);
    }

    public function updateTag(Tag $tag, array $data): void
    {
        $tag->update($data);
    }

    public function deleteTag(Tag $tag): void
    {
        $tag->delete();
    }
} 