<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $user->loadCount(['followers', 'followings', 'posts']);

        $followers = $user->followers()->latest()->limit(50)->get();
        $followings = $user->followings()->latest()->limit(50)->get();

        $posts = $user->posts()->latest()->get();
        
        $media = $user->posts()->whereNotNull('image')->latest()->get();
        
        $replies = $user->comments()->with('post')->latest()->get();

        return view('profile.show', compact('user', 'posts', 'media', 'replies', 'followers', 'followings'));
    }

    public function update(Request $request, User $user)
    {
        if (Auth::id() !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:4096',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description, 
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($user->banner) Storage::disk('public')->delete($user->banner);
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
