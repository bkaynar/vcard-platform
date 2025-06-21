<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use  HasFactory, Notifiable, HasRoles;

    /**
     * Toplu atamaya izin verilen alanlar.
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'address',
        'bio',
        'profile_photo',
        'cover_photo',
        'socials',
    ];

    /**
     * Gizli alanlar (JSON response'ta gösterilmez).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Dönüştürülmesi gereken alanlar.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'socials' => 'array',
    ];

    /**
     * JSON response'ta eklenmesi gereken computed alanlar.
     */
    protected $appends = ['main_role'];

    /**
     * Profil fotoğrafı tam URL olarak döner.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn() =>
            $this->profile_photo
                ? asset("storage/{$this->profile_photo}")
                : asset('images/default-avatar.png')
        );
    }

    /**
     * Kapak fotoğrafı tam URL olarak döner.
     */
    protected function coverPhotoUrl(): Attribute
    {
        return Attribute::get(
            fn() =>
            $this->cover_photo
                ? asset("storage/{$this->cover_photo}")
                : asset('images/default-cover.jpg')
        );
    }

    /**
     * Profil linki (route örneği).
     */
    public function getProfileUrl(): string
    {
        return route('vcard.show', ['username' => $this->username]);
    }

    public function vcardVisits()
    {
        return $this->hasMany(VcardVisit::class);
    }

    /**
     * Kullanıcının ana rolünü döner.
     */
    protected function mainRole(): Attribute
    {
        return Attribute::get(
            fn() => $this->getRoleNames()->first() ?? 'user'
        );
    }
}
