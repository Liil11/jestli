<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(User $user)
    {
        // FIX: Tambahkan baris ini agar VS Code tau ini adalah Model User kamu
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // Mencegah follow diri sendiri
        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'Tidak bisa follow diri sendiri'], 400);
        }

        // Cek apakah sudah follow?
        if ($currentUser->isFollowing($user)) {
            // Kalau sudah, lakukan Unfollow
            $currentUser->followings()->detach($user->id);
            $status = 'unfollowed';
            $message = 'Berhenti mengikuti';
        } else {
            // Kalau belum, lakukan Follow
            $currentUser->followings()->attach($user->id);
            $status = 'followed';
            $message = 'Mengikuti';
        }

        // Return JSON untuk AJAX
        return response()->json([
            'status' => $status,
            'message' => $message,
            'followers_count' => $user->followers()->count()
        ]);
    }
}