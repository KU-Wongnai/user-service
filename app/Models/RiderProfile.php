<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderProfile extends Model
{
    use HasFactory;

    public $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'phone_number',
        'birth_date',
        'bank_account_number',
        'bank_account_name',
        'bank_account_code',
        'book_bank_photo',
        'id_card',
        'id_card_photo',
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