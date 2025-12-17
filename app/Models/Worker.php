<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branche;

class Worker extends Model
{
    use HasFactory;

    protected $table = 'workers';

    protected $fillable = [
        'document',
        'name',
        'lastname',
        'photo',
        'phone',
        'bonding',
        'type',
        'cost_center',
        'zona',
        'region',
        'cargo',
        'status'
    ];

    // protected $casts = [
    //     'zones' => 'array',
    // ];    

    public function branche()
    {
        return $this->belongsTo(Branche::class, 'region'); 
    }
}
