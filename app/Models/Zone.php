<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $table = 'zones';

    protected $fillable = [
        'name',
        'coordinates',
        'address',
        'phone',
        'email',
        'photo',
        'is_shifts',
        'region',
        'salary',
        'others_income',
        'contract_bonus',
        'descriptions',
        'user_id',
        'logo',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
