<?php

namespace App\Http\Controllers;

use App\SummarySheet;
use App\Escuela;
use App\Facultad;
use App\Entry;
use App\Config;
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        date_default_timezone_set('America/Lima');

        $sdate = date("d") . "-" . date("m") . "-" . date("Y");
        $resumen_diario = SummarySheet::select('id', 'estado')->where('fecha', '=', $sdate)->get();
        $resumen_ant = SummarySheet::select('id', 'fecha')->where('total', '=', 0)->where('fecha', '<>', $sdate)->get();
        $facultad = Facultad::select('id')->where('nombre', '=', 'NO TIENE')->get();
        $sequence = Config::select('sequence')->get();

        if(count($resumen_diario) == 0) {
            $resumen = new SummarySheet;
            $resumen->numserie = '';
            $resumen->fecha = $sdate;
            $resumen->estado = 0;
            $resumen->total = 0.00;

            $resumen->save();

            $alerta = $resumen->estado;
        } else {
            $alerta = $resumen_diario[0]['estado'];
        }

        if(count($resumen_ant) != 0) {
            for ($i=0; $i < count($resumen_ant); $i++) { 
                $ing_ant = Entry::select(DB::raw('SUM(monto) AS totti'))->where('fecha', '=', $resumen_ant[$i]->fecha)->where('anulado', '=', true)->get();
                if($ing_ant[0]->totti == 0 || $ing_ant[0]->totti == null){
                    SummarySheet::destroy($resumen_ant[$i]->id);
                }
            }
        }

        if(count($facultad) == 0) {
            $facultad = new Facultad;
            $facultad->codigo = '0000A';
            $facultad->nombre = 'NO TIENE';
            $facultad->save();

            $escuela = new Escuela;
            $escuela->codigo = '0000A';
            $escuela->nombre = 'NO TIENE';
            $escuela->facultad_id = $facultad->id;
            $escuela->save();
        } 

        if(count($sequence) == 0) {
            $sequence = new Config;
            $sequence->sequence = 0;
            $sequence->save();
        }

        $sequence = Config::select('sequence')->get();

        $nssh = SummarySheet::select('id', 'numserie')->orderby('id', 'desc')->first();
        $nssa = SummarySheet::select('id', 'numserie')->where('id', '<',  $nssh->id)->orderby('id', 'desc')->first();
        $tss = SummarySheet::select('id')->get();

        $num_sum_sheet_ant = '';
        $num_sum_sheet_hoy = '';

        if(count($nssa) > 0) {
            $num_sum_sheet_ant = $nssa->numserie;
        }

        if(count($nssh) > 0) {
            $num_sum_sheet_hoy = $nssh->numserie;
        }
        
        $total_sum_sheets = count($tss);

        return view('home')->with('alerta', $alerta)->with('secuencia', $sequence[0]->sequence)->with('num_sum_sheet_ant', $num_sum_sheet_ant)->with('num_sum_sheet_hoy', $num_sum_sheet_hoy)->with('total_sum_sheets', $total_sum_sheets);
    }
}
