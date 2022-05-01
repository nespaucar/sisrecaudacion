<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Escuela extends Model
{
    //A qué tabla hace referencia este modelo
    //El modelo hace referencia a la tabla task
    protected $table = 'escuelas';

    //Los campos que son asignables masivamente
    //Especificar al usuario cuales campos va a llenar
    
    protected $fillable = [
        'codigo', 'nombre', 'facultad_id'
    ];

    //Establecer relacion entre la tarea y el usuario (foraneo)
    public function clientes() {
        return $this->hasMany(Cliente::class);
    }

    public function facultad() {
        return $this->belongsTo(Facultad::class);
    }
}
