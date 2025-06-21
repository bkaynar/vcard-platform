<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;


class ProfileController extends Controller
{
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('User/Profile/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'bio' => $user->bio,
                'profile_photo' => $user->profile_photo,
                'cover_photo' => $user->cover_photo,
                'profile_photo_url' => $user->profile_photo
                    ? asset("storage/{$user->profile_photo}")
                    : asset('images/default-avatar.png'),
                'cover_photo_url' => $user->cover_photo
                    ? asset("storage/{$user->cover_photo}")
                    : asset('images/default-cover.jpg'),
                'socials' => $user->socials ?? [],
            ]
        ]);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5 MB
            'cover_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'socials' => ['nullable', 'array'],
            'socials.*.platform' => ['required', 'string', 'max:50'],
            'socials.*.username' => ['required', 'string', 'max:255'],
        ]);

        // ImageManager oluştur (her iki işlemde de kullanacağız)
        $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

        // Profil fotoğrafı yükleme
        if ($request->hasFile('profile_photo')) {
            // Eski fotoğrafı sil
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $file = $request->file('profile_photo');
            $filename = uniqid('profile_') . '.webp';

            try {
                // Yeni API: read -> encode
                $image = $manager->read($file->getPathname())->encode(new WebpEncoder(), 80);

                $path = 'profile-photos/' . $filename;
                Storage::disk('public')->put($path, (string)$image);

                $user->profile_photo = $path;
            } catch (\Exception $e) {
                return back()->withErrors(['profile_photo' => 'Profil fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        // Kapak fotoğrafı yükleme
        if ($request->hasFile('cover_photo')) {
            // Eski fotoğrafı sil
            if ($user->cover_photo) {
                Storage::disk('public')->delete($user->cover_photo);
            }

            $file = $request->file('cover_photo');
            $filename = uniqid('cover_') . '.webp';

            try {
                $image = $manager->read($file->getPathname())->encode(new WebpEncoder(), 80);

                $path = 'cover-photos/' . $filename;
                Storage::disk('public')->put($path, (string)$image);

                $user->cover_photo = $path;
            } catch (\Exception $e) {
                return back()->withErrors(['cover_photo' => 'Kapak fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        // Sosyal medya verilerini işle
        $socialsArray = [];
        if ($request->has('socials') && is_array($request->socials)) {
            foreach ($request->socials as $social) {
                if (!empty($social['platform']) && !empty($social['username'])) {
                    $socialsArray[$social['platform']] = $social['username'];
                }
            }
        }

        // Kullanıcı bilgilerini güncelle
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'bio' => $request->bio,
            'socials' => $socialsArray,
        ]);

        return redirect()->route('user.profile.edit')->with('success', 'Profil başarıyla güncellendi!');
    }

    public function destroyProfilePhoto()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return redirect()->route('user.profile.edit')->with('success', 'Profil fotoğrafı silindi!');
    }

    public function destroyCoverPhoto()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->cover_photo) {
            Storage::disk('public')->delete($user->cover_photo);
            $user->update(['cover_photo' => null]);
        }

        return redirect()->route('user.profile.edit')->with('success', 'Kapak fotoğrafı silindi!');
    }
}
