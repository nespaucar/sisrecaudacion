<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conceptos_Entries;
use App\Concepto;
use App\Entry;
use App\CostCenter;
use App\SummarySheet;
use App\Cliente;
use App\Tasa;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Reportes extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $conceptos = Concepto::select('conceptos.id', 'conceptos.descripcion')->orderby('descripcion')->get();

        $centercosts = $this->fetchCategoryTreeList();

        date_default_timezone_set('America/Lima');
        $sdate = date("d") . "-" . date("m") . "-" . date("Y");
        $caja = SummarySheet::select('estado')->where('fecha', '=', $sdate)->get();

        $estadocaja = $caja[0]['estado'];

        return view('reportes.index', compact('conceptos', 'centercosts', 'estadocaja'));
    }

    public function crearExcel($vistaurl, $datos, $data2, $date2, $montos, $totales, $totaldetotales, $n1 = '', $n2 = '', $numcuadro, $numeracion, $recibos, $papeletas, $vt, $st = 0)
    {
        $numcuadros = $numcuadro;
        $numeracions = $numeracion;
        $reciboss = $recibos;
        $papeletass = $papeletas;
        $vts = $vt;
        if($datos == ''){
            $totaless = '';
            $montoss = '';
            $data = '';
        } else {
            $totaless = $totales;
            $montoss = $montos;
            $data = $datos;
        }

        $nn1 = $n1;
        $nn2 = $n2;
        $t = $st;
        $totaldetotaless = $this->numletras(number_format($totaldetotales, 2, '.', ''));
        $totaldetotales = number_format($totaldetotales, 2, '.', ',');
        $date = $date2;
        Excel::create('REPORTE - ' . $date, function($excel) use($vistaurl, $data2, $totaless, $montoss, $data, $totaldetotaless, $totaldetotales, $date, $nn1, $nn2, $numcuadros, $numeracions, $reciboss, $papeletass, $vts, $t) {

            $excel->sheet('New sheet', function($sheet) use($vistaurl, $data2, $totaless, $montoss, $data, $totaldetotaless, $totaldetotales, $date, $nn1, $nn2, $numcuadros, $numeracions, $reciboss, $papeletass, $vts, $t) {

                // Font family
                $sheet->setFontFamily('Comic Sans MS');

                // Set font with ->setStyle()`
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  9,
                        'bold'      =>  true,
                    ),
                ));

                $filas = 8;
                if($data == '') {
                    $numceldas = 5;
                    $numceldas2 = count($data2) + 9;
                } else {
                    $numceldas = count($data) + 5;
                    $numceldas2 = count($data2) + 11;
                }

                if($t == 1){
                    $numceldas = count($data) + 6;
                    $numceldas2 = count($data2) + 12;
                    $filas += 3;
                }

                if($t == 3){
                    $numceldas = count($data) + 12;
                    $numceldas2 = count($data2) + 11;
                }
              

                $sheet->getStyle('C' . $filas . ':' . $this->celda($numceldas) . ($numceldas2))->applyFromArray(
                array(
                  'borders' => array(
                    'allborders' => array(
                      'style' => 'thin'
                    )
                  ),
                )); 

                $sheet->loadView($vistaurl)->withTotaldetotaless($totaldetotaless)->withMontoss($montoss)->withData($data)->withTotaldetotales($totaldetotales)->withDate($date)->withData2($data2)->withTotaless($totaless)->withNn1($nn1)->withNn2($nn2)->withNumcuadros($numcuadros)->withNumeracions($numeracions)->withReciboss($reciboss)->withPapeletass($papeletass)->withVts($vts);
            });

        })->download('xlsx');
    }

    public function crearPDF($datos, $data2, $vistaurl, $tipo, $orientacion, $date2, $montos, $totales, $totaldetotales, $n1 = '', $n2 = '', $numcuadro, $numeracion, $recibos = '', $papeletas, $vt)
    {   
        $numeracions = $numeracion;
        $reciboss = $recibos;
        $papeletass = $papeletas;
        $vts = $vt;

        $numcuadros = $numcuadro;
        if($datos == ''){
            $totaless = '';
            $montoss = '';
            $data = '';
        } else {
            $totaless = $totales;
            $montoss = $montos;
            $data = $datos;
        }

        $totaldetotaless = $this->numletras(number_format($totaldetotales, 2, '.', ''));
        $totaldetotales = number_format($totaldetotales, 2, '.', ',');
        $date = $date2;
        $view =  \View::make($vistaurl, compact('data', 'data2', 'date', 'montoss', 'totaless', 'totaldetotaless', 'totaldetotales', 'n1', 'n2', 'numcuadros', 'numeracions', 'reciboss', 'papeletass', 'vts'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('a4', $orientacion); 
        $nombrepdf = 'REPORTE-' . $date . '.PDF';
        //Vertical: $pdf->setPaper ('a4', 'portrait'); 
        
        if($tipo == 1){return $pdf->stream($nombrepdf);}
        if($tipo == 2){return $pdf->download($nombrepdf); }
    }


    /////////////////////

    //Diario----------------------------------------------------------------------------------------------------------

    /////////////////////

    public function CrearReporte($tipo, $date, $datef, $ordenado, $incluir)
    {
        $numcuadro = '';
        $numeracion = '';
        $papeletas = array();
        if($date != $datef) {
            $date2 = 'DEL ' . $this->descriciondefecha($date) . ' AL ' . $this->descriciondefecha($datef);
        } else {
            $sql_numcuadro = SummarySheet::select('numserie', 'np1', 'np2', 'np3', 'np4', 'mp1', 'mp2', 'mp3', 'mp4')->where('fecha', $date)->get();
            if(count($sql_numcuadro) > 0){
                $numcuadro = 'CUADRO Nº ' . $sql_numcuadro[0]->numserie . '-' . substr($date, 6);
            }
            $date2 = $this->descriciondefecha($date);

            if(count($sql_numcuadro) > 0){
                for ($i=1; $i < 5; $i++) { 
                    if($sql_numcuadro[0]['np' . $i] != null){
                        $papeletas[] = $sql_numcuadro[0]['np' . $i];
                        $papeletas[] = $sql_numcuadro[0]['mp' . $i];
                    }
                }
            }

            $sql_papeletas = Entry::select('numrecibo', 'monto')->where('numrecibo', 'LIKE', '%;%')->where('entries.anulado', true)->where('fecha', $date)->orderby('id')->get();

            for ($i=0; $i < count($sql_papeletas); $i++) { 
                $datillos = explode(';', $sql_papeletas[$i]->numrecibo);
                $papeletas[] = $datillos[0];
                $papeletas[] = $sql_papeletas[$i]->monto;
            }

            if(count($papeletas) == 0){
                $papeletas[] = 'NOTA: NO SE HICIERON DEPÓSITOS ESTE DÍA.';
            }
        }

        $ordenado = 'entries.' . $ordenado;

        $orientacion = 'portrait';
        
        $vistaurl = "reportes.repDiario";

        if($incluir == 'true'){

            $orientacion = 'landscape';

            $conceptos = Conceptos_Entries::select('conceptos.id', 'conceptos.descripcion', 'financialclassifiers.codigo as fc', 'budgetclassifiers.codigo as bc')
            ->whereBetween('entries.fecha', [$date, $datef])
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->join('financialclassifiers', 'financialclassifiers.id', '=', 'conceptos.financialclassifier_id')
            ->join('budgetclassifiers', 'budgetclassifiers.id', '=', 'conceptos.budgetclassifier_id')
            ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
            ->distinct()
            ->orderby('conceptos.id')
            ->get();

            //Por recibos
            $montos = Entry::select(DB::raw("STRING_AGG(CONCAT(numrecibo, '@', conceptos__entries.importe, '@', conceptos__entries.concepto_id), '+++') as cadena"))
            ->join('conceptos__entries', 'conceptos__entries.entry_id', '=', 'entries.id')
            ->whereBetween('entries.fecha', [$date, $datef])
            ->groupby('entries.id')
            ->orderby($ordenado)
            ->get();

            //Por conceptos
            $totales = Conceptos_Entries::select(DB::raw("SUM(case WHEN CAST(entries.anulado AS integer) = 1 THEN importe ELSE 0.00 end) AS ds"))
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
            ->whereBetween('entries.fecha', [$date, $datef])
            ->groupby('conceptos.id')
            ->orderby('conceptos.id')
            ->get();
        } 

        $detalles = Entry::select('entries.id', 'entries.fecha', DB::raw("substring(numrecibo from position(';' in numrecibo) + 1 for char_length(numrecibo)) as numrecibo"), 'entries.monto', 'entries.anulado')
            ->whereBetween('entries.fecha', [$date, $datef])
            ->orderby($ordenado)
            ->get();
        
        $tt = Conceptos_Entries::select(DB::raw("SUM(importe) as ds"))
        ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->whereBetween('entries.fecha', [$date, $datef])
        ->where('entries.anulado', 1)->get();

        $totaldetotales = $tt[0]->ds;

        if(count($detalles) > 0){
            $numeracion = 'RECIBOS DESDE EL ' . $detalles[0]->numrecibo . ' AL ' . $detalles[count($detalles) - 1]->numrecibo;
        }
        
        if($tipo == 3) {
            if($incluir == 'true') {
                return $this->crearExcel($vistaurl . '2', $conceptos, $detalles, $date2, $montos, $totales, $totaldetotales, '', '', $numcuadro, $numeracion, '', $papeletas, '');
            } else {
                return $this->crearExcel($vistaurl . '2', '', $detalles, $date2, '', '', $totaldetotales, '', '', $numcuadro, $numeracion, '', $papeletas, '');
            }
            
        } else {
            if($incluir == 'true') { 
                return $this->crearPDF($conceptos, $detalles, $vistaurl, $tipo, $orientacion, $date2, $montos, $totales, $totaldetotales, '', '', $numcuadro, $numeracion, '', $papeletas, '');
            } else {
                return $this->crearPDF('', $detalles, $vistaurl, $tipo, $orientacion, $date2, '', '', $totaldetotales, '', '', $numcuadro, $numeracion, '', $papeletas, '');
            }
        }
    }

    /////////////////////

    //Mensual----------------------------------------------------------------------------------------------------------

    /////////////////////

    public function CrearReporteM($tipo, $date, $datef, $ordenado, $incluir)
    {
        $numeracion = '';
        if($ordenado == 'monto'){
            $ordenado = 'total';
        }
        if($date != $datef) {
            $date2 = 'DE ' . $this->descriciondefecha($date) . ' A ' . $this->descriciondefecha($datef);
        } else {
            $date2 = $this->descriciondefecha($date);
        }

        $dia_final = explode('-', $datef);
        $diaf = $dia_final[0];

        if($diaf == '01' || $diaf == '03' || $diaf == '05' || $diaf == '07' || $diaf == '08' || $diaf == '10' || $diaf == '12'){
            $diaff = '31';
        } else {
            $diaff = '30';
            if($diaf == '02'){
                $diaff = date("d",(mktime(0,0,0,(integer) $datef + 1, 1,(integer) $dia_final[1]) - 1));
            }
        }

        $date = '01-'. $date;
        $datef = $diaff . '-' . $datef;

        $montos = '';

        $orientacion = 'portrait';

        $vistaurl = "reportes.repMensual";

        $detalles = SummarySheet::select('id', 'numserie', 'fecha', 'total')
            ->whereBetween('fecha', [$date, $datef])
            ->where('estado', true)
            ->orderby($ordenado)
            ->get();

        $recibos = array();

        if(count($detalles) != 0) {
            for ($i=0; $i < count($detalles); $i++) { 
                $numserie_ss = $detalles[$i]->numserie;
                $numserie_ss = SummarySheet::select('id')->where('numserie', $numserie_ss)->get();
                $polos_recibos = Entry::select('numrecibo')->where('summary_sheet_id', $numserie_ss[0]->id)->where('numrecibo', 'NOT LIKE', '%;%')->orderby('id')->get();
                $recibitos = 'R-' . $polos_recibos[0]->numrecibo;
                if(count($polos_recibos) > 1) {
                    $recibitos .= '/' . $polos_recibos[count($polos_recibos) - 1]->numrecibo;
                }
                $recibos[] = $recibitos;
                if($i == 0){
                    $polo_inf = $polos_recibos[0]->numrecibo;
                }
            }
            if(count($recibos) > 1){
                $numeracion = 'RECIBOS DESDE EL ' . $polo_inf . ' AL ' . $polos_recibos[count($polos_recibos) - 1]->numrecibo;
            }
        }

        if($incluir == 'true'){

            date_default_timezone_set('America/Lima');
            $sdate = date("d") . "-" . date("m") . "-" . date("Y");

            $orientacion = 'landscape';

            $conceptos = Conceptos_Entries::select('conceptos.id', 'conceptos.descripcion', 'financialclassifiers.codigo as fc', 'budgetclassifiers.codigo as bc')
            ->whereBetween('entries.fecha', [$date, $datef])
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->join('financialclassifiers', 'financialclassifiers.id', '=', 'conceptos.financialclassifier_id')
            ->join('budgetclassifiers', 'budgetclassifiers.id', '=', 'conceptos.budgetclassifier_id')
            ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
            ->where('entries.anulado', true)
            ->distinct()
            ->orderby('conceptos.id')
            ->get();

            $totales = array();

            $totales1 = Conceptos_Entries::select(DB::raw("SUM(case WHEN CAST(entries.anulado AS integer) = 1 THEN importe ELSE 0.00 end) AS ds"), 'conceptos.id')
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
            ->join('summary_sheets', 'entries.summary_sheet_id', '=', 'summary_sheets.id')
            ->where('entries.numrecibo', 'NOT LIKE', '%;%')
            ->where('entries.anulado', true)
            ->where('summary_sheets.estado', true)
            ->whereBetween('entries.fecha', [$date, $datef])
            ->groupby('conceptos.id')
            ->orderby('conceptos.id')
            ->get();

            $totales2 = Conceptos_Entries::select(DB::raw("SUM(case WHEN CAST(entries.anulado AS integer) = 1 THEN importe ELSE 0.00 end) AS ds"), 'conceptos.id')
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
            ->where('entries.numrecibo', 'LIKE', '%;%')
            ->where('entries.anulado', true)
            ->whereBetween('entries.fecha', [$date, $datef])
            ->groupby('conceptos.id')
            ->orderby('conceptos.id')
            ->get();

            if(count($totales1) > 0 || count($totales2) > 0){
                for ($i=0; $i < count($conceptos); $i++) { 
                    $valor = 0;
                    if(count($totales1) > 0){
                        for ($j=0; $j < count($totales1); $j++) { 
                            if($conceptos[$i]->id == $totales1[$j]->id){
                                $valor += $totales1[$j]->ds;
                                break;
                            }
                        }
                    }
                    if(count($totales2) > 0){
                        for ($k=0; $k < count($totales2); $k++) { 
                            if($conceptos[$i]->id == $totales2[$k]->id){
                                $valor += $totales2[$k]->ds;
                                break;
                            }
                        }
                    }
                        
                    $totales[] = $valor;
                }
            }

            $montos = array();

            for ($i=0; $i < count($detalles); $i++) { 
                $det = Conceptos_Entries::select('summary_sheets.id as ssid', DB::raw('SUM(case WHEN CAST(entries.anulado AS integer) = 1 THEN importe ELSE 0.00 end) as tot'), 'conceptos.id as cid')
                ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
                ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
                ->join('summary_sheets', 'summary_sheets.id', '=', 'entries.summary_sheet_id')
                ->where('entries.numrecibo', 'NOT LIKE', '%;%')
                ->where('entries.anulado', true)
                ->where('summary_sheets.fecha', '=', $detalles[$i]->fecha)
                ->groupby('summary_sheets.id')
                ->groupby('conceptos.id')
                ->orderby('summary_sheets.' . $ordenado)
                ->get();

                $montos[] = $det;
            }
        } 

        //Salvar recibos de las ventas a terceros
        $vt1 = Entry::select('fecha', DB::raw('sum(monto) as mont'))->where('numrecibo', 'like', '%;%')->where('entries.anulado', true)->whereBetween('fecha', [$date, $datef])->groupby('fecha')->orderby('fecha')->get();

        $vt = array();

        if(count($vt1) != 0){
            $vt2 = Entry::select('fecha', DB::raw("substring(numrecibo from position(';' in numrecibo) + 1 for char_length(numrecibo)) as numrecibo"), 'conceptos__entries.importe', 'conceptos.id as cid')
            ->join('conceptos__entries', 'conceptos__entries.entry_id', 'entries.id')
            ->join('conceptos', 'conceptos__entries.concepto_id', 'conceptos.id')
            ->where('numrecibo', 'like', '%;%')
            ->where('entries.anulado', true)
            ->whereBetween('entries.fecha', [$date, $datef])
            ->groupby('fecha')
            ->groupby('numrecibo')
            ->groupby('importe')
            ->groupby('cid')
            ->orderby('fecha')->get();

            for ($i=0; $i < count($vt1); $i++) {
                $cadena_recibos = ''; 
                $cadena_conceptos = ''; 
                $k = 0;
                for ($j = $k; $j < count($vt2); $j++) { 
                    if($vt2[$j]->fecha == $vt1[$i]->fecha){
                        $cadena_recibos .= $vt2[$j]->numrecibo . '/';
                    } 
                }

                if($incluir == 'true'){
                    $det2 = Conceptos_Entries::select(DB::raw('SUM(case WHEN CAST(entries.anulado AS integer) = 1 THEN importe ELSE 0.00 end) as tot'), 'conceptos.id as cid')
                    ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
                    ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
                    ->where('entries.numrecibo', 'LIKE', '%;%')
                    ->where('entries.anulado', true)
                    ->where('entries.fecha', '=', $vt1[$i]->fecha)
                    ->groupby('conceptos.id')
                    ->get();
                    for ($l=0; $l < count($det2); $l++) { 
                        $cadena_conceptos .= $det2[$l]->tot . ';' . $det2[$l]->cid . '@';
                    }
                }

                $vt[] = $vt1[$i]->fecha;
                $vt[] = substr($cadena_recibos, 0, -1);
                if($incluir == 'true'){
                    $vt[] = substr($cadena_conceptos, 0, -1);
                } else {
                    $vt[] = '';
                }
                $vt[] = $vt1[$i]->mont;
            }
        }

        $totaldetotales = 0;

        $totaldetotales1 = SummarySheet::select(DB::raw("SUM(total) as ds"))
        ->whereBetween('fecha', [$date, $datef])
        ->where('estado', 1)->get();

        $totaldetotales2 = Conceptos_Entries::select(DB::raw('SUM(case WHEN CAST(entries.anulado AS integer) = 1 THEN importe ELSE 0.00 end) as tot'))
                    ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
                    ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
                    ->where('entries.anulado', true)
                    ->where('entries.numrecibo', 'LIKE', '%;%')
                    ->whereBetween('entries.fecha', [$date, $datef])
                    ->get();

        $totaldetotales = $totaldetotales1[0]->ds;

        if(count($totaldetotales2) > 0){
            $totaldetotales += $totaldetotales2[0]->tot;
        }

        if($tipo == 3) {
            if($incluir == 'true') {
                return $this->crearExcel($vistaurl . '2', $conceptos, $detalles, $date2, $montos, $totales, $totaldetotales, '', '', '', $numeracion, $recibos, '', $vt);
            } else {
                return $this->crearExcel($vistaurl . '2', '', $detalles, $date2, '', '', $totaldetotales, '', '', '', $numeracion, $recibos, '', $vt);
            }
            
        } else {
            if($incluir == 'true') { 
                return $this->crearPDF($conceptos, $detalles, $vistaurl, $tipo, $orientacion, $date2, $montos, $totales, $totaldetotales, '', '', '', $numeracion, $recibos, '', $vt);
            } else {
                return $this->crearPDF('', $detalles, $vistaurl, $tipo, $orientacion, $date2, '', '', $totaldetotales, '', '', '', $numeracion, $recibos, '', $vt);
            }
        }
    }

    /////////////////////

    //Anual

    public function CrearReporteA($tipo, $date, $datef, $ordenado, $incluir)
    {
        if($ordenado != 'monto') {
            $ordenado = 'fech';
        }
        if($date != $datef) {
            $date2 = 'DEL ' . $this->descriciondefecha($date) . ' AL ' . $this->descriciondefecha($datef);
        } else {
            $date2 = $this->descriciondefecha($date);
        }

        $dia_final = explode('-', $datef);
        $diaf = $dia_final[0];

        $date = '01-01-'. $date;
        $datef ='31-12-'. $datef;

        $montos = '';

        $orientacion = 'portrait';

        $vistaurl = "reportes.repAnual";

        $detalles = Entry::select(DB::raw("CONCAT('M-', to_char(fecha, 'mm/yyyy')) as numserie"), DB::raw("to_char(fecha, 'mm/yyyy') as fech"), DB::raw("sum(monto) as total"))
            ->where('anulado', true)
            ->whereBetween('fecha', [$date, $datef])
            ->groupby("fech")
            ->orderby($ordenado)
            ->get();

        if($incluir == 'true'){

            date_default_timezone_set('America/Lima');
            $sdate = date("d") . "-" . date("m") . "-" . date("Y");

            $orientacion = 'landscape';

            $conceptos = Conceptos_Entries::select('conceptos.id', 'conceptos.descripcion', 'financialclassifiers.codigo as fc', 'budgetclassifiers.codigo as bc')
            ->whereBetween('fecha', [$date, $datef])
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->join('financialclassifiers', 'financialclassifiers.id', '=', 'conceptos.financialclassifier_id')
            ->join('budgetclassifiers', 'budgetclassifiers.id', '=', 'conceptos.budgetclassifier_id')
            ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
            ->where('entries.anulado', true)
            ->distinct()
            ->orderby('conceptos.id')
            ->get();

            $totales = Conceptos_Entries::select(DB::raw("SUM(case WHEN CAST(entries.anulado AS integer) = 1 THEN importe ELSE 0.00 end) AS ds"))
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
            ->where('entries.anulado', true)
            ->whereBetween('fecha', [$date, $datef])
            ->groupby('conceptos.id')
            ->orderby('conceptos.id')
            ->get();

            $montos = array();

            for ($i=0; $i < count($detalles); $i++) { 
                $det = Conceptos_Entries::select(DB::raw('SUM(case WHEN CAST(entries.anulado AS integer) = 1 THEN importe ELSE 0.00 end) as tot'), 'conceptos.id as cid')
                ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
                ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
                ->where('entries.anulado', true)
                ->where(DB::raw("to_char(entries.fecha, 'mm/yyyy')"), '=', $detalles[$i]->fech)
                ->groupby(DB::raw("to_char(entries.fecha, 'mm/yyyy')"))
                ->groupby('conceptos.id')
                ->get();

                $montos[] = $det;
            }
        } 

        $tt1 = Entry::select(DB::raw("SUM(monto) as ds"))
        ->where('entries.anulado', true)
        ->whereBetween('fecha', [$date, $datef])
        ->get();

        $totaldetotales = $tt1[0]->ds;

        if($tipo == 3) {
            if($incluir == 'true') {
                return $this->crearExcel($vistaurl . '2', $conceptos, $detalles, $date2, $montos, $totales, $totaldetotales, '', '', '', '', '', '', '');
            } else {
                return $this->crearExcel($vistaurl . '2', '', $detalles, $date2, '', '', $totaldetotales, '', '', '', '', '', '', '');
            }
            
        } else {
            if($incluir == 'true') { 
                return $this->crearPDF($conceptos, $detalles, $vistaurl, $tipo, $orientacion, $date2, $montos, $totales, $totaldetotales, '', '', '', '', '', '', '');
            } else {
                return $this->crearPDF('', $detalles, $vistaurl, $tipo, $orientacion, $date2, '', '', $totaldetotales, '', '', '', '', '', '', '');
            }
        }
    }
    
    /////////////////////

    //PorConceptosuCentroDeCostos

    public function CrearReporteCCC($tipo, $date, $datef, $idconcepto, $idcostcenter)
    {
        $n_concepto = 'TODOS';
        $n_centrocosto = 'TODOS';
        $tabla = 'conceptos.id';

        if($idconcepto == '0') {
            $idconcepto = '%%';
        } else {
            $array_id = explode(';', $idconcepto);
            if(count($array_id) == 2){
                $busc = Tasa::select('descripcion')->where('id', $array_id[1])->get();
                $tabla = 'entries.tasa_id';
                $idconcepto = $array_id[1];
            } else {
                $busc = Concepto::select('descripcion')->where('id', $idconcepto)->get();
            }
            
            $n_concepto = $busc[0]->descripcion;
        }

        if($idcostcenter == '0') {
            $idcostcenter = '%%';
        } else {
            $buscc = CostCenter::select('name')->where('id', $idcostcenter)->get();
            $n_centrocosto = $buscc[0]->name;
        }

        if($date != $datef) {
            $date2 = 'DEL ' . $this->descriciondefecha($date) . ' AL ' . $this->descriciondefecha($datef);
        } else {
            $date2 = $this->descriciondefecha($date);
        }

        $orientacion = 'portrait';
        
        $vistaurl = "reportes.repPorConcCostCenter";

        $listacostcenters = $this->array_id($idcostcenter, '');

        if(count($listacostcenters) > 0) {
            $where = '';
            $i = 0;
            foreach ($listacostcenters as $costcenter) {
                if(count($listacostcenters) == 1) {
                    $where .= "IN (" . $costcenter . ')';
                } else {
                    if($i == 0) {
                    $where .= "IN (" . $costcenter . ', ';
                } else if($i != count($listacostcenters) - 1) {
                        $where .= $costcenter . ', ';
                    } else if($i == count($listacostcenters) - 1){
                        if($idcostcenter == '%%'){
                            $where .= $costcenter .')';
                        } else {
                            $where .= $idcostcenter .', ';
                            $where .= $costcenter .')';
                        }
                    }
                }
                $i++;                
            }

            $detalles = Cliente::select('nombres', 'apellidop', 'apellidom', DB::raw('CASE WHEN anulado = true THEN SUM(importe) ELSE 0.00 END as totall'), DB::raw("substring(numrecibo from position(';' in numrecibo) + 1 for char_length(numrecibo)) as cod"), 'fecha')
            ->join('entries', 'clientes.id', '=', 'entries.cliente_id')
            ->join('cost_centers', 'cost_centers.id', '=', 'entries.costcenter_id')
            ->join('conceptos__entries', 'conceptos__entries.entry_id', '=', 'entries.id')
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->where(DB::raw('cast(' . $tabla . ' as text)'), 'LIKE', $idconcepto)
            ->where('entries.anulado', true)
            ->whereBetween('fecha', [$date, $datef])
            ->WhereRaw('cost_centers.id '. $where)
            ->groupby('nombres', 'apellidop', 'apellidom', 'fecha', 'numrecibo','anulado')
            ->orderby('numrecibo')
            ->get();

            $tt = Cliente::select(DB::raw('SUM(importe) as ds'))
            ->join('entries', 'clientes.id', '=', 'entries.cliente_id')
            ->join('cost_centers', 'cost_centers.id', '=', 'entries.costcenter_id')
            ->join('conceptos__entries', 'conceptos__entries.entry_id', '=', 'entries.id')
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->where(DB::raw('cast(' . $tabla . ' as text)'), 'LIKE', $idconcepto)
            ->where('entries.anulado', true)
            ->whereBetween('fecha', [$date, $datef])
            ->WhereRaw('cost_centers.id '. $where)
            ->get();

            $total = $tt[0]->ds;

        } else {
             $detalles = Cliente::select('nombres', 'apellidop', 'apellidom', DB::raw('SUM(importe) as totall'), 'numrecibo as cod', 'fecha')
            ->join('entries', 'clientes.id', '=', 'entries.cliente_id')
            ->join('cost_centers', 'cost_centers.id', '=', 'entries.costcenter_id')
            ->join('conceptos__entries', 'conceptos__entries.entry_id', '=', 'entries.id')
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->where(DB::raw('cast(cost_centers.id as text)'), 'LIKE', $idcostcenter)
            ->where(DB::raw('cast(' . $tabla . ' as text)'), 'LIKE', $idconcepto)
            ->where('entries.anulado', true)
            ->whereBetween('fecha', [$date, $datef])
            ->groupby('nombres', 'apellidop', 'apellidom', 'fecha', 'numrecibo')
            ->orderby('numrecibo')
            ->get();

            $tt = Cliente::select(DB::raw('SUM(importe) as ds'))
            ->join('entries', 'clientes.id', '=', 'entries.cliente_id')
            ->join('cost_centers', 'cost_centers.id', '=', 'entries.costcenter_id')
            ->join('conceptos__entries', 'conceptos__entries.entry_id', '=', 'entries.id')
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->where(DB::raw('cast(cost_centers.id as text)'), 'LIKE', $idcostcenter)
            ->where(DB::raw('cast(' . $tabla . ' as text)'), 'LIKE', $idconcepto)
            ->where('entries.anulado', true)
            ->whereBetween('fecha', [$date, $datef])
            ->get();

            $total = $tt[0]->ds;

        }

           
        if($tipo == 3) {
            return $this->crearExcel($vistaurl . '2', '', $detalles, $date2, '', '', $total, $n_concepto, $n_centrocosto, '', '', '', '', '', 1);
        } else {
            return $this->crearPDF('', $detalles, $vistaurl, $tipo, $orientacion, $date2, '', '', $total, $n_concepto, $n_centrocosto, '', '', '', '', '');
        }
    }

    /////////////////////

    //Terceros----------------------------------------------------------------------------------------------------------

    /////////////////////

    public function CrearReporteTerceros($tipo, $date, $datef, $ordenado, $rango)
    {
        $numcuadro = '';

        if($rango == 'D'){
            $date2 = $this->descriciondefecha($date);

            $sql_numcuadro = SummarySheet::select('numserie')->where('fecha', $date)->get();
            if(count($sql_numcuadro) > 0){
                $numcuadro = 'CUADRO Nº ' . $sql_numcuadro[0]->numserie . '-' . substr($date, 6);
            }
        } else if($rango == 'M'){
            $date2 = $this->descriciondefecha(substr($date, 3, strlen($date)-2));
        } else {
            $date2 = $this->descriciondefecha(substr($date, -4));
        }

        $ordenado = 'entries.' . $ordenado;
        
        $vistaurl = "reportes.repTerceros";

        $orientacion = 'landscape';

        $conceptos = Conceptos_Entries::select('conceptos.id', 'conceptos.descripcion', 'financialclassifiers.codigo as fc', 'budgetclassifiers.codigo as bc')
        ->whereBetween('entries.fecha', [$date, $datef])
        ->where('numrecibo', 'LIKE', '%;%')
        ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
        ->join('financialclassifiers', 'financialclassifiers.id', '=', 'conceptos.financialclassifier_id')
        ->join('budgetclassifiers', 'budgetclassifiers.id', '=', 'conceptos.budgetclassifier_id')
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->distinct()
        ->orderby('conceptos.id')
        ->get();

        //Por recibos
        $montos = Entry::select(DB::raw("STRING_AGG(CONCAT(numrecibo, '@', conceptos__entries.importe - 0.18*conceptos__entries.importe, '@', conceptos__entries.concepto_id), '+++') as cadena"))
        ->join('conceptos__entries', 'conceptos__entries.entry_id', '=', 'entries.id')
        ->where('numrecibo', 'LIKE', '%;%')
        ->whereBetween('entries.fecha', [$date, $datef])
        ->groupby('entries.id')
        ->orderby($ordenado)
        ->get();

        //Por conceptos
        $totales = Conceptos_Entries::select(DB::raw("SUM(case WHEN CAST(entries.anulado AS integer) = 1 THEN importe - 0.18*importe ELSE 0.00 end) AS ds"))
        ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->where('numrecibo', 'LIKE', '%;%')
        ->whereBetween('entries.fecha', [$date, $datef])
        ->groupby('conceptos.id')
        ->orderby('conceptos.id')
        ->get();

        $totales2 = Conceptos_Entries::select(DB::raw("SUM(case when monto >= 700 then monto*0.12 else 0.00 end) as detraccion"), DB::raw("SUM(monto)*0.18 as igv"))
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->where('numrecibo', 'LIKE', '%;%')
        ->whereBetween('entries.fecha', [$date, $datef])
        ->get();

        $detalles = Entry::select('entries.id', 'entries.fecha', 'clientes.nombres', DB::raw("substring(numrecibo from position(';' in numrecibo) + 1 for char_length(numrecibo)) as numrecibo"), DB::raw("substring(numrecibo from 1 for position(';' in numrecibo) - 1) as voucher"), DB::raw('(case when monto >= 700 then monto*0.12 else 0.00 end) as detraccion'), DB::raw("(case when numrecibo like '%E001%' then 'FACTURA' else 'BOLETA' end) as tipodoc"), DB::raw('0.18*monto as igv'), 'entries.monto', 'entries.anulado', 'numserie', 'clientes.dni')
            ->join('clientes', 'clientes.id', 'entries.cliente_id')
            ->join('summary_sheets', 'summary_sheets.id', 'entries.summary_sheet_id')
            ->whereBetween('entries.fecha', [$date, $datef])
            ->where('entries.numrecibo', 'LIKE', '%;%')
            ->orderby($ordenado)
            ->get();
        
        $tt = Conceptos_Entries::select(DB::raw("SUM(importe) as ds"))
        ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->where('entries.numrecibo', 'LIKE', '%;%')
        ->whereBetween('entries.fecha', [$date, $datef])
        ->where('entries.anulado', 1)->get();

        $totaldetotales = $tt[0]->ds;
        
        if($tipo == 3) {
            return $this->crearExcel($vistaurl . '2', $conceptos, $detalles, $date2, $montos, $totales, $totaldetotales, '', '', $numcuadro, '', '', '', $totales2, 3);
            
        } else {
            return $this->crearPDF($conceptos, $detalles, $vistaurl, $tipo, $orientacion, $date2, $montos, $totales, $totaldetotales, '', '', $numcuadro, '', '', '', $totales2);
        }
    }
    
    /////////////////////

    protected function _dia($nom_dia) {
        if($nom_dia =='Monday'){
            $nom_dia = 'LUNES';
        }else if($nom_dia =='Tuesday'){
            $nom_dia = 'MARTES';
        }else if($nom_dia =='Wednesday'){
            $nom_dia = 'MIERCOLES';
        }else if($nom_dia =='Thursday'){
            $nom_dia = 'JUEVES';
        }else if($nom_dia =='Friday'){
            $nom_dia = 'VIERNES';
        }else if($nom_dia =='Saturday'){
            $nom_dia = 'SABADO';
        }else {
            $nom_dia = 'DOMINGO';
        }

        return $nom_dia;
    }

    protected function _mes($smes) {
        if($smes =='01'){
            $nombre_mes = 'ENERO';
        }else if($smes =='02'){
            $nombre_mes = 'FEBRERO';
        }else if($smes =='03'){
            $nombre_mes = 'MARZO';
        }else if($smes =='04'){
            $nombre_mes = 'ABRIL';
        }else if($smes =='05'){
            $nombre_mes = 'MAYO';
        }else if($smes =='06'){
            $nombre_mes = 'JUNIO';
        }else if($smes =='07'){
            $nombre_mes = 'JULIO';
        }else if($smes =='08'){
            $nombre_mes = 'AGOSTO';
        }else if($smes =='09'){
            $nombre_mes = 'SETIEMBRE';
        }else if($smes =='010'){
            $nombre_mes = 'OCTUBRE';
        }else if($smes =='011'){
            $nombre_mes = 'NOVIEMBRE';
        }else {
            $nombre_mes = 'DICIEMBRE';
        }

        return $nombre_mes;
    }

    protected function descriciondefecha($fecha) {

        $fecha0 = explode("-", $fecha);

        if(sizeof($fecha0) == 3) {
            date_default_timezone_set('America/Lima');
            $nom_dia = jddayofweek(cal_to_jd(CAL_GREGORIAN, $fecha0[1],$fecha0[0], $fecha0[2]) , 1);

            $sdia = $fecha0[0];
            $smes = $fecha0[1];
            $sanno = $fecha0[2];

            $nom_dia = $this->_dia($nom_dia);
            $nom_mes = $this->_mes($smes);

            return $nom_dia . ' ' . $sdia . ' DE ' . $nom_mes . ' DEL AÑO ' . $sanno;

        } else if(sizeof($fecha0) == 2) {
            $smes = $fecha0[0];
            $sanno = $fecha0[1];

            $nom_mes = $this->_mes($smes);

            return $nom_mes . ' DEL ' . $sanno;
        } else {

            return 'AÑO ' . $this->milmillon($fecha);
        }
    }

    protected function numletras($numero) { 
        $tempnum = explode('.',$numero); 

        if ($tempnum[0] !== ""){ 
            $numf = $this->milmillon($tempnum[0]); 
            if ($numf == "UNO") { 
                $numf = substr($numf, 0, -1); 
            } 
            $TextEnd = $numf.' CON ';
        } if ($tempnum[1] == "") { 
            $tempnum[1] = "00"; 
        } 
        $TextEnd .= $tempnum[1] ; 
        $TextEnd .= "/100 SOLES"; 
        return $TextEnd; 
    } 


    protected function unidad($numuero){ 
        switch ($numuero) { 
            case 9: { 
                $numu = "NUEVE"; 
                break; 
            } 
            case 8: { 
                $numu = "OCHO"; 
                break; 
            } 
            case 7: { 
                $numu = "SIETE"; 
                break; 
            } 
            case 6: { 
                $numu = "SEIS"; 
                break; 
            } 
            case 5: { 
                $numu = "CINCO"; 
                break; 
            } 
            case 4: { 
                $numu = "CUATRO"; 
                break; 
            } 
            case 3: { 
                $numu = "TRES"; 
                break; 
            } 
            case 2: { 
                $numu = "DOS"; 
                break; 
            } 
            case 1: { 
                $numu = "UNO"; 
                break; 
            } 
            case 0: { 
                $numu = "CERO"; 
                break; 
            } 
        } 
        return $numu; 
    } 

    protected function decena($numdero){ 

        if ($numdero >= 90 && $numdero <= 99) { 
            $numd = "NOVENTA "; 
            if ($numdero > 90) 
            $numd = $numd."Y ".($this->unidad($numdero - 90)); 
        } else if ($numdero >= 80 && $numdero <= 89) { 
            $numd = "OCHENTA "; 
            if ($numdero > 80) 
            $numd = $numd."Y ".($this->unidad($numdero - 80)); 
        } else if ($numdero >= 70 && $numdero <= 79) { 
            $numd = "SETENTA "; 
            if ($numdero > 70) 
            $numd = $numd."Y ".($this->unidad($numdero - 70)); 
        } else if ($numdero >= 60 && $numdero <= 69) { 
            $numd = "SESENTA "; 
            if ($numdero > 60) 
            $numd = $numd."Y ".($this->unidad($numdero - 60)); 
        } else if ($numdero >= 50 && $numdero <= 59) { 
            $numd = "CINCUENTA "; 
            if ($numdero > 50) 
            $numd = $numd."Y ".($this->unidad($numdero - 50)); 
        } else if ($numdero >= 40 && $numdero <= 49) { 
            $numd = "CUARENTA "; 
            if ($numdero > 40) 
            $numd = $numd."Y ".($this->unidad($numdero - 40)); 
        } else if ($numdero >= 30 && $numdero <= 39) { 
            $numd = "TREINTA "; 
            if ($numdero > 30) 
            $numd = $numd."Y ".($this->unidad($numdero - 30)); 
        } else if ($numdero >= 20 && $numdero <= 29) { 
            if ($numdero == 20) 
            $numd = "VEINTE "; 
            else 
            $numd = "VEINTI".($this->unidad($numdero - 20)); 
        } else if ($numdero >= 10 && $numdero <= 19) { 
            switch ($numdero){ 
                case 10: { 
                    $numd = "DIEZ "; 
                    break; 
                } 
                case 11: { 
                    $numd = "ONCE "; 
                    break; 
                } 
                case 12: { 
                    $numd = "DOCE "; 
                    break; 
                } 
                case 13: { 
                    $numd = "TRECE "; 
                    break; 
                } 
                case 14: { 
                    $numd = "CATORCE "; 
                    break; 
                } 
                case 15: { 
                    $numd = "QUINCE "; 
                    break; 
                } 
                case 16: { 
                    $numd = "DIECISEIS "; 
                    break; 
                } 
                case 17: { 
                    $numd = "DIECISIETE "; 
                    break; 
                } 
                case 18: { 
                    $numd = "DIECIOCHO "; 
                    break; 
                } 
                case 19: { 
                    $numd = "DIECINUEVE "; 
                    break; 
                } 
            } 
        } else 
        $numd = $this->unidad($numdero); 
        return $numd; 
    } 

    protected function centena($numc){ 
        if ($numc >= 100) { 
            if ($numc >= 900 && $numc <= 999) { 
                $numce = "NOVECIENTOS "; 
                if ($numc > 900) 
                $numce = $numce.($this->decena($numc - 900)); 
            } else if ($numc >= 800 && $numc <= 899) { 
                $numce = "OCHOCIENTOS "; 
                if ($numc > 800) 
                $numce = $numce.($this->decena($numc - 800)); 
            } else if ($numc >= 700 && $numc <= 799) { 
                $numce = "SETECIENTOS "; 
                if ($numc > 700) 
                $numce = $numce.($this->decena($numc - 700)); 
            } else if ($numc >= 600 && $numc <= 699) { 
                $numce = "SEISCIENTOS "; 
                if ($numc > 600) 
                $numce = $numce.($this->decena($numc - 600)); 
            } else if ($numc >= 500 && $numc <= 599) { 
                $numce = "QUINIENTOS "; 
                if ($numc > 500) 
                $numce = $numce.($this->decena($numc - 500)); 
            } else if ($numc >= 400 && $numc <= 499) { 
                $numce = "CUATROCIENTOS "; 
                if ($numc > 400) 
                $numce = $numce.($this->decena($numc - 400)); 
            } else if ($numc >= 300 && $numc <= 399) { 
                $numce = "TRESCIENTOS "; 
                if ($numc > 300) 
                $numce = $numce.($this->decena($numc - 300)); 
            } else if ($numc >= 200 && $numc <= 299) { 
                $numce = "DOSCIENTOS "; 
                if ($numc > 200) 
                $numce = $numce.($this->decena($numc - 200)); 
            } else if ($numc >= 100 && $numc <= 199) { 
                if ($numc == 100) 
                    $numce = "CIEN "; 
                else 
                    $numce = "CIENTO ".($this->decena($numc - 100)); 
            } 
        } 
        else 
        $numce = $this->decena($numc); 

        return $numce; 
    } 

    protected function miles($nummero){ 
        if ($nummero >= 1000 && $nummero < 2000){ 
            $numm = "MIL ".($this->centena($nummero%1000)); 
        } 
        if ($nummero >= 2000 && $nummero <10000){ 
            $numm = $this->unidad(Floor($nummero/1000))." MIL ".($this->centena($nummero%1000)); 
        } 
        if ($nummero < 1000) 
        $numm = $this->centena($nummero); 

        return $numm; 
    } 

    protected function decmiles($numdmero){ 
        if ($numdmero == 10000) 
            $numde = "DIEZ MIL"; 
        if ($numdmero > 10000 && $numdmero <20000){ 
            $numde = $this->decena(Floor($numdmero/1000))."MIL ".($this->centena($numdmero%1000)); 
        } 
        if ($numdmero >= 20000 && $numdmero <100000){ 
            $numde = $this->decena(Floor($numdmero/1000))." MIL ".($this->miles($numdmero%1000)); 
        } 
        if ($numdmero < 10000) 
        $numde = $this->miles($numdmero); 

        return $numde; 
    } 

    protected function cienmiles($numcmero){ 
        if ($numcmero == 100000) 
            $num_letracm = "CIEN MIL"; 
        if ($numcmero >= 100000 && $numcmero <1000000){ 
            $num_letracm = $this->centena(Floor($numcmero/1000))." MIL ".($this->centena($numcmero%1000)); 
        } 
        if ($numcmero < 100000) 
        $num_letracm = $this->decmiles($numcmero); 
        return $num_letracm; 
    } 

    protected function millon($nummiero){ 
        if ($nummiero >= 1000000 && $nummiero <2000000){ 
            $num_letramm = "UN MILLON ".($this->cienmiles($nummiero%1000000)); 
        } 
        if ($nummiero >= 2000000 && $nummiero <10000000){ 
            $num_letramm = $this->unidad(Floor($nummiero/1000000))." MILLONES ".($this->cienmiles($nummiero%1000000)); 
        } 
        if ($nummiero < 1000000) 
        $num_letramm = $this->cienmiles($nummiero); 

        return $num_letramm; 
    } 

    protected function decmillon($numerodm){ 
        if ($numerodm == 10000000) 
            $num_letradmm = "DIEZ MILLONES"; 
        if ($numerodm > 10000000 && $numerodm <20000000){ 
            $num_letradmm = $this->decena(Floor($numerodm/1000000))."MILLONES ".($this->cienmiles($numerodm%1000000)); 
        } 
        if ($numerodm >= 20000000 && $numerodm <100000000){ 
            $num_letradmm = $this->decena(Floor($numerodm/1000000))." MILLONES ".($this->millon($numerodm%1000000)); 
        } 
        if ($numerodm < 10000000) 
        $num_letradmm = $this->millon($numerodm); 

        return $num_letradmm; 
    } 

    protected function cienmillon($numcmeros){ 
        if ($numcmeros == 100000000) 
            $num_letracms = "CIEN MILLONES"; 
        if ($numcmeros >= 100000000 && $numcmeros <1000000000){ 
            $num_letracms = $this->centena(Floor($numcmeros/1000000))." MILLONES ".($this->millon($numcmeros%1000000)); 
        } 
        if ($numcmeros < 100000000) 
        $num_letracms = $this->decmillon($numcmeros); 
        return $num_letracms; 
    } 

    protected function milmillon($nummierod){ 
        if ($nummierod >= 1000000000 && $nummierod <2000000000){ 
            $num_letrammd = "MIL ".($this->cienmillon($nummierod%1000000000)); 
        } 
        if ($nummierod >= 2000000000 && $nummierod <10000000000){ 
            $num_letrammd = $this->unidad(Floor($nummierod/1000000000))." MIL ".($this->cienmillon($nummierod%1000000000)); 
        } 
        if ($nummierod < 1000000000) 
        $num_letrammd = $this->cienmillon($nummierod); 

        return $num_letrammd; 
    } 

    protected function celda($num, $cadena = '') {
        if($num > 26){
            $num2 = intdiv($num, 26);
            $num = $num % 26;
            $cadena .= $this->celda_letra($num2);
            return $this->celda($num, $cadena);
        }
        $cadena .= $this->celda_letra($num);
        return $cadena;
    }

    protected function celda_letra($num) {
        switch ($num) {
            case 1:  return "A";
            case 2:  return "B";
            case 3:  return "C";
            case 4:  return "D";
            case 5:  return "E";
            case 6:  return "F";
            case 7:  return "G";
            case 8:  return "H";
            case 9:  return "I";
            case 10: return "J";
            case 11: return "K";
            case 12: return "L";
            case 13: return "M";
            case 14: return "N";
            case 15: return "O";
            case 16: return "P";
            case 17: return "Q";
            case 18: return "R";
            case 19: return "S";
            case 20: return "T";
            case 21: return "U";
            case 22: return "V";
            case 23: return "W";
            case 24: return "X";
            case 25: return "Y";
            case 26: return "Z";
        }
    }

    protected function fetchCategoryTreeList($parent = 0, $user_tree_array = '', $espacios = 1, $faltantes = 0) {
        $faltantes += $espacios;

        $espaciado = '';

        for ($i=0; $i < $faltantes; $i++) { 
            $espaciado .= '&nbsp&nbsp&nbsp&nbsp';
        }

        if (!is_array($user_tree_array)){
            $user_tree_array = array();
        }

        $re_cc = CostCenter::select('id', 'name', 'parent_id', 'codigo', DB::raw('(select count(b.id) from cost_centers b where b.parent_id = cost_centers.id) as t'))
        ->where('parent_id', '=', $parent)
        ->orderby('id', 'ASC')
        ->get();

        if (count($re_cc) > 0) {
            foreach ($re_cc as $row) {
                if($row['t'] == 0) {
                    $user_tree_array[] = "<option value='". $row['id']."'>". $espaciado . $row['name']."</option>";
                } else {
                    $user_tree_array[] = "<option value='". $row['id']."'>" . $espaciado . $row['name']."</option>";
                    $user_tree_array = $this->fetchCategoryTreeList($row['id'], $user_tree_array, $faltantes, $espacios);
                }
            }
        } 
      return $user_tree_array;
    }

    protected function array_id($parent, $array_id = '') {

        if (!is_array($array_id))
            $array_id = array();

        $re_cc = CostCenter::select('id', DB::raw('(select count(b.id) from cost_centers b where b.parent_id = cost_centers.id) as t'))
        ->where('parent_id', '=', (integer) $parent)
        ->orderby('id', 'ASC')
        ->get();

        if (count($re_cc) > 0) {
            foreach ($re_cc as $row) {
                if($row['t'] == 0) {
                    $array_id[] = $row['id'];
                } else {
                    $array_id[] = $row['id'];
                    $array_id = $this->array_id($row['id'], $array_id);
                }
            }
        } 
      return $array_id;
    }
}   