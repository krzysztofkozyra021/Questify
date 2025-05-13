<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileImageService
{
    /**
     * Update user's profile image
     *
     * @param User $user
     * @param UploadedFile $image
     * @return array{success: bool, profile_image_url: string}
     */
    public function updateProfileImage(User $user, UploadedFile $image): array
    {
        // Delete old image if exists
        $this->deleteProfileImage($user);

        // Store new image
        $path = $image->store('profile-images', 'public');
        
        $user->update([
            'profile_image' => $path,
        ]);

        return [
            'success' => true,
            'profile_image_url' => url('storage/' . $path),
        ];
    }

    /**
     * Get user's profile image URL
     *
     * @param User $user
     * @return array{profile_image_url: string|null}
     */
    public function getProfileImageUrl(User $user): array
    {
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            return [
                'profile_image_url' => url('storage/' . $user->profile_image),
            ];
        }

        // If no valid image exists, clear the profile_image field
        if ($user->profile_image) {
            $user->update(['profile_image' => null]);
        }

        return [
            'profile_image_url' => null,
        ];
    }

    /**
     * Delete user's profile image
     *
     * @param User $user
     * @return void
     */
    private function deleteProfileImage(User $user): void
    {
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }
    }
} 