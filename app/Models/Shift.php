<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';

    protected $fillable = [
        'name_zone',
        'zone_id',
        'schedule_id',
        'day_since',
        'day_until',
        'val1',
        'val2',
        'n_workers',
        'salario_base',
    ];

    public function personalShifts()
    {
        return $this->hasMany(PersonalShift::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
