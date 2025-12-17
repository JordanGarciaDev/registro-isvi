<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametrization extends Model
{
    use HasFactory;

    protected $table = 'parametrization';

    protected $fillable = [
        'n_horas_semanales',
        'rango_hora_inicio_nocturno',
        'rango_hora_final_nocturno',
        'user_id',
        'status'
    ];
}
