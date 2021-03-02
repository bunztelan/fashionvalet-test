<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Booking extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'driver_id',
        'passenger_id',
        'state',
        'country_id',
        'fare',
        'created_at_local',
    ];

    /**
     * Booking state
     * 
     * @var array
     */
    public const STATE = [
        'COMPLETED',
        'CANCELLED_PASSENGER',
        'CANCELLED_DRIVER',
    ];

    public function getCreateAtLocalAttribute($value)
    {
        return Carbon::parse($value)->timestamp;
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
