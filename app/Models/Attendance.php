<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_photo',
        'check_in_face_score',
        'check_in_address',
        'check_out',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_photo',
        'check_out_face_score',
        'check_out_address',
        'qr_token',
        'status',
        'notes',
        'work_duration',
        'late_duration',
        'early_leave_duration',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return $this->status === 'valid';
    }

    public function isLate(): bool
    {
        return $this->late_duration > 0;
    }

    public function hasEarlyLeave(): bool
    {
        return $this->early_leave_duration > 0;
    }
}
