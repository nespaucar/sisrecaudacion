<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{
    //A qué tabla hace referencia este modelo
    protected $table = 'conceptos';

    //Los campos que son asignables masivamente
    //Especificar al usuario cuales campos va a llenar
    
    protected $fillable = [
    	'descripcion', 'financialclassifier_id', 'budgetclassifier_id',
    ];

    //Establecer relacion entre la tarea y el usuario (foraneo)
    public function clientes() {
    	return $this->hasMany(Cliente::class);
    }

    public function conceptos_entries() {
        return $this->hasMany(Conceptos_Entries::class);
    }

    public function tasas() {
        return $this->hasMany(Tasa::class);
    }

    public function facultad() {
    	return $this->belongsTo(Facultad::class);
    }

    public function financialclassifiers() {
    	return $this->belongsTo(FinancialClassifier::class);
    }

    public function budgetclassifiers() {
    	return $this->belongsTo(BudgetClassifier::class);
    }
}
