<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    public $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'phone_number',
        'birth_date',
        'address',
        'avatar',
        'student_id',
        'faculty',
        'major',
        'favorite_food',
        'allergy_food',
        'point',
    ];

    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }
}