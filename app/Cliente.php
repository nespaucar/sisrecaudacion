<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    //A qué tabla hace referencia este modelo
    //El modelo hace referencia a la tabla task
    protected $table = 'clientes';

    //Los campos que son asignables masivamente
    //Especificar al usuario cuales campos va a llenar
    
    protected $fillable = [
        'codigo', 'tipo', 'nombres', 'apellidop', 'apellidom', 'escuela_id', 'dni'
    ];

    public function escuela() {
        return $this->belongsTo(Escuela::class);
    }

    public function debs() {
        return $this->hasMany(Deb::class);
    }

}
