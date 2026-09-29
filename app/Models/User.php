<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'password',
        'accepted_terms_at',
        'terms_version',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
        'mfa_code',
        'mfa_code_expires_at',
        'mfa_secret',
        'deleted_at',
        'accepted_terms_at',
        'terms_version',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mfa_code_expires_at' => 'datetime',
            'accepted_terms_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }

    public function setPasswordAttribute(string $value): void
    {
        $pepper = config('app.password_pepper', '');
        $this->attributes['password'] = Hash::make($value . $pepper);
    }

    public function verifyPassword(string $password): bool
    {
        $pepper = config('app.password_pepper', '');
        return Hash::check($password . $pepper, $this->password);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function cleanupRelatedData(): void
    {
        $id = $this->id;
        $email = $this->email;

        $profile = Profile::where('user_id', $id)->first();
        if ($profile && $profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
            Storage::disk('public')->delete($profile->avatar);
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            Contract::withTrashed()->where('user_1_id', $id)->orWhere('user_2_id', $id)->forceDelete();
            Exchange::where('user_proponente_id', $id)->orWhere('user_receptor_id', $id)->forceDelete();
            Service::withTrashed()->where('user_id', $id)->forceDelete();
            Message::where('sender_id', $id)->orWhere('receiver_id', $id)->forceDelete();
            Profile::where('user_id', $id)->forceDelete();
            DB::table('approvals')->where('user_id', $id)->delete();
            DB::table('user_roles')->where('user_id', $id)->delete();
            DB::table('sessions')->where('user_id', $id)->delete();
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            $this->roles()->detach();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
