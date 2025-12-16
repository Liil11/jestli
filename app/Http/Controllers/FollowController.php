<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(User $user)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'Tidak bisa follow diri sendiri'], 400);
        }

        if ($currentUser->isFollowing($user)) {
            $currentUser->followings()->detach($user->id);
            $status = 'unfollowed';
            $message = 'Berhenti mengikuti';
        } else {
            $currentUser->followings()->attach($user->id);
            $status = 'followed';
            $message = 'Mengikuti';
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'followers_count' => $user->followers()->count()
        ]);
    }
}
