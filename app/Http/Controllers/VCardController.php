<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VcardVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VCardController extends Controller
{
    public function show(Request $request, $username)
    {
        // Kullanıcıyı username ile bul
        $user = User::where('username', $username)->firstOrFail();

        // Ziyaret kaydı oluştur
        VcardVisit::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'visited_at' => now(),
        ]);

        return Inertia::render('VCard/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'bio' => $user->bio,
                'profile_photo_url' => $user->profile_photo
                    ? asset("storage/{$user->profile_photo}")
                    : asset('images/default-avatar.png'),
                'cover_photo_url' => $user->cover_photo
                    ? asset("storage/{$user->cover_photo}")
                    : asset('images/default-cover.jpg'),
                'socials' => $user->socials,
            ]
        ]);
    }
}
