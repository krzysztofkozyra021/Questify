<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function updateProfileImage(Request $request)
    {
        $request->validate([
            "profile_image" => "required|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);

        $user = Auth::user();

        // Delete old image if exists
        if ($user->profile_image && Storage::disk("public")->exists($user->profile_image)) {
            Storage::disk("public")->delete($user->profile_image);
        }

        $path = $request->file("profile_image")->store("profile-images", "public");

        $user->update([
            "profile_image" => $path,
        ]);

        $url = url("storage/" . $path);

        return back()->with([
            "success" => true,
            "profile_image_url" => $url,
        ]);
    }

    public function getProfileImage(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->profile_image && Storage::disk("public")->exists($user->profile_image)) {
            $url = url("storage/" . $user->profile_image);

            return response()->json([
                "profile_image_url" => $url,
            ]);
        }

        // If no valid image exists, clear the profile_image field
        if ($user && $user->profile_image) {
            $user->update(["profile_image" => null]);
        }

        return response()->json([
            "profile_image_url" => null,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render("Profile/Edit", [
            "mustVerifyEmail" => $request->user() instanceof MustVerifyEmail,
            "status" => session("status"),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty("email")) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route("profile.edit");
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            "password" => ["required", "current_password"],
        ]);

        $user = $request->user();

        // Delete user statistics first
        if ($user->userStatistics) {
            $user->userStatistics->delete();
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to("/");
    }
}
