<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password','needs_password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'needs_password' => 'boolean',
        ];
    }

    /**
     * User ka vendor profile.
     */
    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    /**
     * Filament panel access permissions.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Admin panel access
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['super_admin', 'admin']);
        }

        // Vendor panel access
        if ($panel->getId() === 'vendor') {
            return $this->vendor()
                ->where('status', 'active')
                ->where('verification_status', 'approved')
                ->exists();
        }

        return false;
    }

    public function addresses()
    {
        return $this->hasMany(Address::class)->latest();
    }
}
