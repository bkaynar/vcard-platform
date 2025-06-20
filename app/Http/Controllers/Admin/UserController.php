<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

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
            'profile_photo' => 'nullable|image|max:1024',
            'cover_photo' => 'nullable|image|max:2048',
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
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $data['cover_photo'] = $request->file('cover_photo')->store('cover-photos', 'public');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:' . User::class . ',' . $user->id,
            'email' => 'required|string|email|max:255|unique:' . User::class . ',' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:1024',
            'cover_photo' => 'nullable|image|max:2048',
            'socials' => 'nullable|array',
            'socials.*.platform' => 'required|string|max:50',
            'socials.*.username' => 'required|string|max:255',
        ]);

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
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $data['cover_photo'] = $request->file('cover_photo')->store('cover-photos', 'public');
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
