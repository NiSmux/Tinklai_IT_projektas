<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkelbimoNuotrauka extends Model
{
    protected $table = 'skelbimo_nuotrauka';
    public $timestamps = false;

    protected $fillable = [
        'skelbimas_id',
        'failo_kelias',
        'pozicija'
    ];

    public function skelbimas()
    {
        return $this->belongsTo(Skelbimas::class, 'skelbimas_id');
    }
}
