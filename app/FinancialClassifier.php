<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancialClassifier extends Model
{
    //A qué tabla hace referencia este modelo
    //El modelo hace referencia a la tabla task
    protected $table = 'financialclassifiers';

    //Los campos que son asignables masivamente
    //Especificar al usuario cuales campos va a llenar
    
    protected $fillable = [
    	'codigo',
    ];

    //Establecer relacion entre la tarea y el usuario (foraneo)
    public function conceptos() {
    	return $this->hasMany(Concepto::class);
    }
}
