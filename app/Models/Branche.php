<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zone;
use App\Models\User;

class Branche extends Model
{
    use HasFactory;

    protected $table = 'branches';

    protected $fillable = ['name', 'zones', 'user_id', 'status'];

    protected $casts = [
        'zones' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getZoneNamesAttribute()
    {
        $ids = json_decode($this->zones, true);
        return Zone::whereIn('id', $ids)->pluck('name')->toArray();
    }
}
