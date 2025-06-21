<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;
use Exception;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::role('user')->with('roles')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:' . User::class,
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:5120',   // 5 MB
            'cover_photo'   => 'nullable|image|max:5120',   // 5 MB
            'socials' => 'nullable|array',
            'socials.*.platform' => 'required|string|max:50',
            'socials.*.username' => 'required|string|max:255',
        ]);

        $data = $request->only(['name', 'username', 'email', 'phone', 'address', 'bio']);
        $data['password'] = Hash::make($request->password);

        // Sosyal medya verilerini işle
        if ($request->has('socials') && is_array($request->socials)) {
            $socials = [];
            foreach ($request->socials as $social) {
                if (!empty($social['platform']) && !empty($social['username'])) {
                    $socials[$social['platform']] = $social['username'];
                }
            }
            $data['socials'] = $socials;
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = uniqid('profile_') . '.webp';

            // Webp olarak dönüştür ve public diskine kaydet
            try {
                $image = Image::make($file)->encode('webp', 80); // 80 kalite (isteğe bağlı)

                // 'profile-photos' klasörüne kaydet
                $path = 'profile-photos/' . $filename;
                \Storage::disk('public')->put($path, (string) $image);

                $data['profile_photo'] = $path;
            } catch (Exception $e) {
                return back()->withErrors(['profile_photo' => 'Profil fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        if ($request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            $filename = uniqid('cover_') . '.webp';

            try {
                $image = Image::make($file)->encode('webp', 80);

                $path = 'cover-photos/' . $filename;
                \Storage::disk('public')->put($path, (string) $image);

                $data['cover_photo'] = $path;
            } catch (Exception $e) {
                return back()->withErrors(['cover_photo' => 'Kapak fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        $user = User::create($data);

        $user->assignRole('user');

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla oluşturuldu!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Gelen verileri logla
        \Log::info('Gelen veriler:', $request->all());

        // Gelen verileri detaylı logla
        \Log::info('Request tüm veriler:', ['data' => $request->all()]);
        \Log::info('Request input verileri:', ['data' => $request->input()]);
        \Log::info('Request name:', ['value' => $request->name]);
        \Log::info('Request username:', ['value' => $request->username]);
        \Log::info('Request email:', ['value' => $request->email]);

        // Gelen isteğin header bilgilerini logla
        \Log::info('Request headers:', ['headers' => $request->headers->all()]);
        \Log::info('Request method:', ['method' => $request->method()]);
        \Log::info('Request content:', ['content' => $request->getContent()]);

        // Doğrulama kurallarını kontrol et
        // Doğrulama hatalarını logla
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:' . User::class . ',' . $user->id,
                'email' => 'required|string|email|max:255|unique:' . User::class . ',' . $user->id,
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'profile_photo' => 'nullable|image|max:5120',   // 5 MB
                'cover_photo'   => 'nullable|image|max:5120',   // 5 MB
                'socials' => 'nullable|array',
                'socials.*.platform' => 'required|string|max:50',
                'socials.*.username' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Doğrulama hatası:', $e->errors());
            throw $e;
        }

        $data = $request->only(['name', 'username', 'email', 'phone', 'address', 'bio']);

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // Sosyal medya verilerini işle
        if ($request->has('socials') && is_array($request->socials)) {
            $socials = [];
            foreach ($request->socials as $social) {
                if (!empty($social['platform']) && !empty($social['username'])) {
                    $socials[$social['platform']] = $social['username'];
                }
            }
            $data['socials'] = $socials;
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = uniqid('profile_') . '.webp';

            // Webp olarak dönüştür ve public diskine kaydet
            try {
                $image = Image::make($file)->encode('webp', 80); // 80 kalite (isteğe bağlı)

                // 'profile-photos' klasörüne kaydet
                $path = 'profile-photos/' . $filename;
                \Storage::disk('public')->put($path, (string) $image);

                $data['profile_photo'] = $path;
            } catch (Exception $e) {
                return back()->withErrors(['profile_photo' => 'Profil fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        if ($request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            $filename = uniqid('cover_') . '.webp';

            try {
                $image = Image::make($file)->encode('webp', 80);

                $path = 'cover-photos/' . $filename;
                \Storage::disk('public')->put($path, (string) $image);

                $data['cover_photo'] = $path;
            } catch (Exception $e) {
                return back()->withErrors(['cover_photo' => 'Kapak fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index');
    }
}
