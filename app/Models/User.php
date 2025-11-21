<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'employee_id',
        'base_salary',
        'position',
        'supervisor_id',
    ];

    /**
     * Get the role of the user
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the supervisor of the user
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get all subordinates of the user
     */
    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /**
     * Get all attendances of the user
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all overtimes of the user
     */
    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    /**
     * Get all leaves of the user
     */
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Get all payrolls of the user
     */
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    /**
     * Check if user is atasan
     */
    public function isAtasan(): bool
    {
        return $this->role?->name === 'atasan';
    }

    /**
     * Check if user is karyawan
     */
    public function isKaryawan(): bool
    {
        return $this->role?->name === 'karyawan';
    }

    /**
     * Get role name
     */
    public function getRoleName(): string
    {
        return $this->role?->display_name ?? 'Unknown';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
