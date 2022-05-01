<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tasa extends Model
{
    //A qué tabla hace referencia este modelo
    //El modelo hace referencia a la tabla task
    protected $table = 'tasas';

    //Los campos que son asignables masivamente
    //Especificar al usuario cuales campos va a llenar
    
    protected $fillable = [
        'descripcion', 'p_actual', 'igv', 'concepto_id',
    ];

    //Establecer relacion entre la tasa y el concepto (foraneo)
    public function concepto() {
        return $this->belongsTo(Concepto::class);
    }
}

