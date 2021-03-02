<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone_number',
        'email',
    ];

    /** Define relationship one to many with booking models */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'driver_id', 'id');
    }
}
