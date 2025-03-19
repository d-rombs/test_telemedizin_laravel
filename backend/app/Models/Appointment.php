<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'patient_email',
        'date_time',
        'status'
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    /**
     * Get the doctor that owns the appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
