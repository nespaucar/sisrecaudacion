<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    //A qué tabla hace referencia este modelo
    //El modelo hace referencia a la tabla task
    protected $table = 'facultads';

    //Los campos que son asignables masivamente
    //Especificar al usuario cuales campos va a llenar
    
    protected $fillable = [
    	'codigo', 'nombre'
    ];

    //Establecer relacion entre la tarea y el usuario (foraneo)
    public function escuelas() {
    	return $this->hasMany(Escuela::class);
    }
}
