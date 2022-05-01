<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    //A qué tabla hace referencia este modelo
    protected $table = 'configs';

    protected $fillable = [
    	'sequence',
    ];
}
