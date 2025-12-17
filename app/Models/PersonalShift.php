<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalShift extends Model
{
    use HasFactory;

    protected $table = 'personal_shifts';

    protected $fillable = [
        'shift_id',
        'personal_id',
        'schedule_id',
        'day_since',
        'day_until',
        'date_programation',
        'turn'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'personal_id');
    }
}
