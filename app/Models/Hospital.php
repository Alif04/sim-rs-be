<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

use App\Models\Doctor;
use App\Models\Specialist;

class Hospital extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'photo',
        'city',
        'post_code',
        'about',
    ];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function specialists()
    {
        return $this->belongsToMany(Specialist::class, 'hospital_specialists');
    }

    public function getPhotoUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return url(Storage::url($value));
    }
}
