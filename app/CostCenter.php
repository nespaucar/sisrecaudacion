<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    //A qué tabla hace referencia este modelo
    protected $table = 'cost_centers';

    //Los campos que son asignables masivamente
    //Especificar al usuario cuales campos va a llenar
    
    protected $fillable = [
    	'name', 'parent_id', 'codigo',
    ];

    public function entries() {
    	return $this->hasMany(Entry::class);
    }
}
