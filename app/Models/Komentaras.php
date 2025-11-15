<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komentaras extends Model
{
    protected $table = 'komentaras';
    public $timestamps = false;

    protected $fillable = [
        'skelbimas_id',
        'vartotojas_id',
        'zinute',
        'data'
    ];

    public function vartotojas()
    {
        return $this->belongsTo(User::class, 'vartotojas_id');
    }

    public function skelbimas()
    {
        return $this->belongsTo(Skelbimas::class, 'skelbimas_id');
    }
}
