<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Get all users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this is admin role
     */
    public function isAdmin(): bool
    {
        return $this->name === 'admin';
    }

    /**
     * Check if this is atasan role
     */
    public function isAtasan(): bool
    {
        return $this->name === 'atasan';
    }

    /**
     * Check if this is karyawan role
     */
    public function isKaryawan(): bool
    {
        return $this->name === 'karyawan';
    }
}
