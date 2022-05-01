<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conceptos_Entries extends Model
{
    //A qué tabla hace referencia este modelo
    protected $table = 'conceptos__entries';

    //Los campos que son asignables masivamente
    //Especificar al usuario cuales campos va a llenar
    
    protected $fillable = [
    	'cantidad', 'p_real', 'descripcion', 'importe',
    ];

    public function entry() {
    	return $this->belongsTo(Entry::class);
    }

    public function concepto() {
    	return $this->belongsTo(Concepto::class);
    }
}
