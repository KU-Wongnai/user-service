<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'birth_date',
        'bank_account_number',
        'id_card',
        'avatar',
        'student_id',
        'faculty',
        'major',
        'desire_location',
    ];

    public function rider()
    {
        return $this->belongsTo(RiderProfile::class);
    }
}