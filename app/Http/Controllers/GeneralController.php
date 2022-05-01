<?php

namespace App\Http\Controllers;

use App\CostCenter;
use App\Facultad;
use App\Escuela;
use App\Cliente;
use App\Concepto;
use App\Personal;
use App\SummarySheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function eliminar1($id, $table)
    {
    	if($table == 'facultads') {
        	$facultads = Facultad::select(DB::raw('count(escuelas.id) as escuelas'))
	        ->join('escuelas', 'escuelas.facultad_id', '=', 'facultads.id')
	        ->where('facultads.id', '=', $id)
	        ->get();

	        if($facultads[0]->escuelas == 0){
	            return response()->json([
	                'mensaje' => 'NO TIENE ninguna ESCUELA asignada a esta facultad. Si la elimina no afectará la integridad de su información.',
	            ]);
	        } else {
	            return response()->json([
	                'mensaje' => 'TIENE escuela(s) asignada(s): <br> <b style="color:#42FF33">CANTIDAD DE ESCUELAS: ' . $facultads[0]->escuelas . '<br> <b style="color:#FF3371">La eliminación, causará pérdidas de información.</b>',
	            ]);            
	        }
        }

        else if($table == 'escuelas') {
        	$escuelas = Escuela::select(DB::raw('count(clientes.id) as clientes'))
	        ->join('clientes', 'escuelas.id', '=', 'clientes.escuela_id')
	        ->where('escuelas.id', '=', $id)
	        ->get();

	        if($escuelas[0]->clientes == 0){
	            return response()->json([
	                'mensaje' => 'NO TIENE ningún CLIENTE asignado a esta escuela. Si la elimina no afectará la integridad de su información.',
	            ]);
	        } else {
	            return response()->json([
	                'mensaje' => 'TIENE alumno(s) asignado(s): <br> <b style="color:#42FF33">CANTIDAD DE ALUMNOS: ' . $escuelas[0]->clientes . '<br> <b style="color:#FF3371">La eliminación, causará pérdidas de información.</b>',
	            ]);            
	        }
        }

        else if($table == 'clientes') {
        	$clientes = Cliente::select(DB::raw('count(entries.id) as ventas'))
	        ->join('entries', 'clientes.id', '=', 'entries.cliente_id')
	        ->where('clientes.id', '=', $id)
	        ->get();

	        if($clientes[0]->ventas == 0){
	            return response()->json([
	                'mensaje' => 'NO TIENE ninguna VENTA asignada a este cliente. Si lo elimina no afectará la integridad de su información.',
	            ]);
	        } else {
	            return response()->json([
	                'mensaje' => 'TIENE venta(s) asignada(s): <br> <b style="color:#42FF33">CANTIDAD DE VENTAS: ' . $clientes[0]->ventas . '<br> <b style="color:#FF3371">La eliminación, causará pérdidas de información.</b>',
	            ]);            
	        }
        }

        else if($table == 'cost_centers') {
        	$costcenter = CostCenter::select(DB::raw('count(entries.id) as entradas'), DB::raw('sum(entries.monto) as monto'))
	        ->join('entries', 'entries.costcenter_id', '=', 'cost_centers.id')
	        ->where('cost_centers.id', '=', $id)
	        ->get();

	        if($costcenter[0]->entradas == 0){
	            return response()->json([
	                'mensaje' => 'NO TIENE ninguna venta identificada, si lo elimina no afectará la integridad de su información.',
	            ]);
	        } else {
	            return response()->json([
	                'mensaje' => 'TIENE venta(s) identificada(s): <br> <b style="color:#42FF33">CANTIDAD DE VENTAS: ' . $costcenter[0]->entradas . '. <br> MONTO TOTAL: S/. ' . $costcenter[0]->monto . '.</b><br> <b style="color:#FF3371">La eliminación, causará pérdidas de información.</b>',
	            ]);            
	        }
        }

        else if($table == 'conceptos') {
        	$conceptos = Concepto::select(DB::raw('count(conceptos__entries) as ventas'))
	        ->join('conceptos__entries', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
	        ->where('conceptos.id', '=', $id)
	        ->get();

	        if($conceptos[0]->ventas == 0){
	            return response()->json([
	                'mensaje' => 'NO TIENE ninguna VENTA asignada a este concepto. Si lo elimina no afectará lo integridad de su información.',
	            ]);
	        } else {
	            return response()->json([
	                'mensaje' => 'TIENE venta(s) asignada(s): <br> <b style="color:#42FF33">CANTIDAD DE VENTAS: ' . $conceptos[0]->ventas . '<br> <b style="color:#FF3371">La eliminación, causará pérdidas de información.</b>',
	            ]);            
	        }
        }

        else if($table == 'personal') {
            return response()->json([
                'mensaje' => 'Si aceptas, no podrás recuperar la información del Trabajador(a).',
            ]);            
        }
    }

    public function add_numserie_sum_sheet_hoy($numserie){
        $css = SummarySheet::select('id')->where('numserie', $numserie)->get();
        if(count($css) != 0) {
        	return response()->json([
                'estado' => 'false',
            ]); 
        } else {
        	$ssh = SummarySheet::select('id')->orderby('id', 'desc')->first();
        	$resumen = SummarySheet::find($ssh->id);
            $resumen->numserie = $numserie;
            $resumen->save();
        	return response()->json([
                'estado' => 'true',
            ]); 
        }
    }

    public function eliminar2($id, $table)
    {
        $sms = '<div class="row"><div class="col-md-12"><p class="text-center" style="color:red">HUBO UN PROBLEMA AL ELIMINAR</p></div></div>';
        $sms1 = '<div class="row"><div class="col-md-12"><p class="text-center" style="color:green">';
        $sms2 = 'ELIMINAD(O) CORRECTAMENTE</p></div></div>';

        if($table == 'facultads') {
        	try {
	            $facultad = Facultad::find($id);
	            $facultad->delete();
	            if(!$facultad->delete()) {
	                return response()->json([
		                'mensaje' => $sms1. 'FACULTAD ' . $sms2,
		            ]);
	            } else {
	                return response()->json([
		                'mensaje' => $sms,
		            ]);  
	            }
	        } catch(\Exception $e) {
	            return response()->json([
	                'mensaje' => $sms,
	            ]); 
	        }
        }

        else if($table == 'escuelas') {

	        try {
	            $escuela = Escuela::find($id);
	            $escuela->delete();
	            if(!$escuela->delete()) {
	                return response()->json([
		                'mensaje' => $sms1. 'ESCUELA ' . $sms2,
		            ]);
	            } else {
	                return response()->json([
		                'mensaje' => $sms,
		            ]);  
	            }
	        } catch(\Exception $e) {
	            return response()->json([
	                'mensaje' => $sms,
	            ]); 
	        }
        }

        else if($table == 'clientes') {
        	
	        try {
	            $cliente = Cliente::find($id);
	            $cliente->delete();
	            if(!$cliente->delete()) {
	                return response()->json([
		                'mensaje' => $sms1. 'CLIENTE ' . $sms2,
		            ]);
	            } else {
	                return response()->json([
		                'mensaje' => $sms,
		            ]);  
	            }
	        } catch(\Exception $e) {
	            return response()->json([
	                'mensaje' => $sms,
	            ]); 
	        }
        }

        else if($table == 'conceptos') {
        	
	        try {
	            $concepto = Concepto::find($id);
	            $concepto->delete();
	            if(!$concepto->delete()) {
	               return response()->json([
		                'mensaje' => $sms1. 'CONCEPTO ' . $sms2,
		            ]);
	            } else {
	                return response()->json([
		                'mensaje' => $sms,
		            ]);  
	            }
	        } catch(\Exception $e) {
	            return response()->json([
	                'mensaje' => $sms,
	            ]); 
	        }
        }

        else if($table == 'cost_centers') {

        	try {
	            $cost_center = CostCenter::find($id);
	            $cost_center->delete();
	            if(!$cost_center->delete()) {
	               return response()->json([
		                'mensaje' => $sms1. 'CENTRO DE COSTO ' . $sms2,
		            ]);
	            } else {
	                return response()->json([
		                'mensaje' => $sms,
		            ]);  
	            }
	        } catch(\Exception $e) {
	            return response()->json([
	                'mensaje' => $sms,
	            ]); 
	        }
        }

        else if($table == 'personal') {

        	try {
	            $personal = Personal::find($id);
	            $personal->delete();
	            if(!$personal->delete()) {
	               return response()->json([
		                'mensaje' => $sms1. 'TRABAJADOR ' . $sms2,
		            ]);
	            } else {
	                return response()->json([
		                'mensaje' => $sms,
		            ]);  
	            }
	        } catch(\Exception $e) {
	            return response()->json([
	                'mensaje' => $sms,
	            ]); 
	        }
        }
    }

    public function prueba_funcion()
    {
    	$prueba = DB::select('select * from prueba4();');
    	dd($prueba);
    }
}
