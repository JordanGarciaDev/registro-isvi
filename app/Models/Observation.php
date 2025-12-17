<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;

    protected $table = 'observations';

    protected $fillable = [
        'personal_shift_id',
        'path_document',
        'date_until',
        'date_since',
        'personal_register',
        'personal_releva',
        'prioridad',
        'status'
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function personalShift()
    {
        return $this->belongsTo(PersonalShift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
