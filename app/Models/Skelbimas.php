<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skelbimas extends Model
{
    protected $table = 'skelbimas';
    public $timestamps = false;

    protected $fillable = [
        'vartotojas_id',
        'pavadinimas',
        'aprasymas',
        'perziuros',
        'sukurimo_data',
        'redagavimo_data',
        'busena',
        'kaina',
        'galioja_iki',
        'aktyvus'
    ];
    protected $casts = [
        'galioja_iki' => 'date',
    ];

    public function vartotojas()
    {
        return $this->belongsTo(User::class, 'vartotojas_id');
    }

    public function nuotraukos()
    {
        return $this->hasMany(SkelbimoNuotrauka::class, 'skelbimas_id')
                    ->orderBy('pozicija');
    }

    public function komentarai()
    {
        return $this->hasMany(Komentaras::class, 'skelbimas_id');
    }

    public function isExpired(): bool
    {
        return $this->galioja_iki && now()->greaterThan($this->galioja_iki);
    }
}
