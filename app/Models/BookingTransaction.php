<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingTransaction extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'status',
        'started_at',
        'time_at',
        'sub_total',
        'tax_total',
        'grand_total',
        'proof',
    ];

    protected $casts = [
        'started_at' => 'date',
        'time_at' => 'date:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function getProofUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return url(Storage::url($value));
    }
}
