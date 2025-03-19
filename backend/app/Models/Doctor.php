<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    /**
     * Die Attribute, die zuweisbar sind.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'specialization_id'];

    /**
     * Die standardmäßig mit diesem Modell zu ladenden Beziehungen.
     *
     * @var array<int, string>
     */
    protected $with = ['specialization'];

    /**
     * Die Attribute, die versteckt werden sollen.
     *
     * @var array<int, string>
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Get the specialization that the doctor belongs to.
     */
    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    /**
     * Get the appointments for the doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the time slots for the doctor.
     */
    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }
}
