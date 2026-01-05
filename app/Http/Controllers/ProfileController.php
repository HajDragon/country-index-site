<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if (! $user instanceof User) {
            abort(403);
        }

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $name = time().'_'.$image->getClientOriginalName();
            $path = $image->storeAs('profile_images', $name, 'public');

            // Optional: delete old image
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $user->profile_image = $path;
            $user->save();
        }

        return back()->with('success', 'Profile image uploaded successfully!');
    }
}
