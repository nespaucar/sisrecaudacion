<?php

namespace App\Http\Controllers;

use App\Concepto;
use App\CostCenter;
use App\Cliente;
use App\Entry;
use App\Conceptos_Entries;
use App\SummarySheet;
use App\Deb;
use App\Config;
use App\Tasa;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\DB;

class IngresoController extends Controller
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

        $sequence = Config::select('sequence')->get();

        $nssh = SummarySheet::select('id', 'numserie')->orderby('id', 'desc')->first();

        $hoja_resumen = $nssh->numserie;

        return view('ingreso.index', compact('conceptos', 'centercosts', 'estadocaja', 'sequence', 'hoja_resumen'));
    }

    public function buscarCliente(Request $request, $codigo)
    {
        $cliente = Cliente::select('clientes.id', 'clientes.codigo', DB::raw("(clientes.nombres || ' ' || clientes.apellidop || ' ' || clientes.apellidom) as nombress"), 'clientes.dni', 'escuelas.nombre as escuela')
        ->join('escuelas', 'escuelas.id', '=', 'clientes.escuela_id')
        ->where('clientes.codigo', 'ILIKE', '%'.$codigo.'%')
        ->orWhere(DB::raw("(clientes.nombres || ' ' || clientes.apellidop || ' ' || clientes.apellidom)"), 'ILIKE', '%'.$codigo.'%')->get();
        return response()->json([
            'nombres' => $cliente[0]->nombress,
            'id' => $cliente[0]->id,
            'codigo' => $cliente[0]->codigo,
            'escuela' => $cliente[0]->escuela,
            'dni' => $cliente[0]->dni,
        ]);
    }

    public function ventas_hoy() 
    {
        date_default_timezone_set('America/Lima');
        $sdate = date("d") . "-" . date("m") . "-" . date("Y");

        $totaldetotales = Conceptos_Entries::select(DB::raw("SUM(importe) as ds"))
        ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->where('entries.numrecibo', 'NOT LIKE', '%;%')
        ->where('entries.fecha', $sdate)
        ->where('entries.anulado', 1)->get();

        $caja = SummarySheet::select('estado')->where('fecha', '=', $sdate)
        ->get();

        $dias = SummarySheet::select('summary_sheets.id', 'summary_sheets.fecha', DB::raw('SUM(entries.monto) as total'))
        ->join('entries', 'entries.summary_sheet_id', '=', 'summary_sheets.id')
        ->where('entries.numrecibo', 'NOT LIKE', '%;%')
        ->whereColumn('summary_sheets.fecha', '=', 'entries.fecha')
        ->where('summary_sheets.fecha', '<>', $sdate)
        ->where('entries.anulado', '=', true)
        ->where('summary_sheets.estado', '=', false)
        ->groupby('summary_sheets.id')
        ->orderby('summary_sheets.fecha')
        ->get();

        $alertas = 0;

        if($caja[0]['estado'] == 0) {
            $alertas += 1;
            
        }
        if(count($dias) != 0) {
            $alertas += 1;
        }
                
        $detalles = Entry::select('entries.id',  DB::raw("substring(numrecibo from position(';' in numrecibo) + 1 for char_length(numrecibo)) as numrecibo"), 'entries.fecha', 'entries.monto', 'entries.estado', 'entries.anulado', 'clientes.nombres', 'clientes.apellidop', 'clientes.apellidom', 'clientes.dni')
        ->join('clientes', 'clientes.id', '=', 'entries.cliente_id')
        ->where('entries.fecha', $sdate)
        ->orderby('entries.id')->get();
        return view('ventas_hoy', compact('detalles', 'caja', 'dias', 'alertas', 'totaldetotales'));
    }

    public function ingreso($numrecibo, $id_cliente, $fecha, $datos, $monto, $estado, $id_cc, $idtasa, $tipo)
    {
        $anul_ant = '';
        if($tipo == 2){
            $eee = Entry::select(DB::raw('MAX(id) as max'))->get();
            $ing_anterior = Entry::find($eee[0]->max);
            $ing_anterior->anulado = 0;
            $ing_anterior->save();
            $anul_ant = '<h2 style="color:red; text-align: center">Recibo Nº ' . $ing_anterior->numrecibo . ' Anulado.</h2>';
        }
        $arraynumrecibo = explode(";", $numrecibo);
        try {
            ////////////////////////////////////////////////////////////////////////////////////////////////
            $exist_ing = Entry::select('id')->where('numrecibo', '=', $numrecibo)->get();

            if(count($exist_ing) != 0){
                return response()->json([
                    'alerta' => 'No se puede registrar Recibos con igual número. Actualice el correlativo.'.count($exist_ing),
                    'sequence' => $numrecibo,
                    'anul_ant' => $anul_ant,
                ]);
            } 

            $ingreso = new Entry;
            $ingreso->numrecibo = (String) ($numrecibo);
            $ingreso->fecha = $fecha;
            $ingreso->monto = urldecode($monto);
            $ingreso->estado = $estado;
            $ingreso->anulado = 1;
            $ingreso->cliente_id = $id_cliente;
            $ingreso->costcenter_id = $id_cc;
            $ingreso->tasa_id = $idtasa;

            $impdetalle = array();

            date_default_timezone_set('America/Lima');
            $sdate = date("d") . "-" . date("m") . "-" . date("Y");
            $resumen = SummarySheet::select('id')->where('fecha', '=', $sdate)->get();

            $ingreso->summary_sheet_id = $resumen[0]['id'];

            if($ingreso->save()) {

                if(count($arraynumrecibo) != 2){
                    $secuencia = Config::find(1);
                    $secuencia->sequence = (int) $numrecibo + 1;
                    $secuencia->save();
                }

                $array = explode("↓↓↓", $datos);
                $p_realtotal = 0;
                $importe_total = 0;
                for ($i = 0; $i < count($array); $i++) { 
                    $detalles = explode("@@@", $array[$i]);

                    $detalle = new Conceptos_Entries;
                    $detalle->cantidad = $detalles[4];
                    $detalle->p_real = $detalles[2];
                    $detalle->descripcion = $detalles[1];
                    $detalle->importe = $detalles[3];
                    $detalle->entry_id = $ingreso->id;
                    $detalle->concepto_id = $detalles[0];

                    $p_realtotal += (float) $detalles[2];
                    $importe_total += (float) $detalles[3];

                    $impdetalle[] = $detalles[4];
                    $impdetalle[] = $detalles[1]; 
                    $impdetalle[] = $detalles[3];

                    $detalle->save();
                }

                if($p_realtotal != $importe_total) {
                    $deuda = new Deb;
                    $deuda->numrecibo = $numrecibo;
                    $deuda->total = $p_realtotal;
                    $deuda->deuda = $p_realtotal - $importe_total;
                    $deuda->cliente_id = $id_cliente;

                    $deuda->save();
                }

                $cliente = Cliente::select('nombres', 'apellidop', 'apellidom', 'nombre')
                ->join('escuelas', 'clientes.escuela_id', '=', 'escuelas.id')
                ->where('clientes.id', '=', $id_cliente)->get();

                $nombrecliente = $cliente[0]->nombres . ' ' . $cliente[0]->apellidop . ' ' . $cliente[0]->apellidom;
                $nombrescuela = $cliente[0]->nombre;

                if($nombrescuela != 'NO TIENE') {
                    $this->impCompPago($nombrecliente, $nombrescuela, $importe_total, $impdetalle);
                }

                if(count($arraynumrecibo) != 2){
                    $sequence = (int) $numrecibo + 1;
                } else {
                    $sequence = '';
                }

                return response()->json([
                    'alerta' => 'Registrado Correctamente',
                    'sequence' => $sequence,
                    'anul_ant' => $anul_ant,
                ]);
            } else {
                return response()->json([
                    'alerta' => 'No se pudo registrar el Ingreso',
                    'anul_ant' => $anul_ant,
                ]);
            }
        } catch(\Exception $e) {
            return response()->json([
                'alerta' => 'No se pudo registrar el Ingreso',
                'anul_ant' => $anul_ant,
            ]);
        }
    }

    public function reimprimir($numrecibo, $id_cliente, $datos)
    {
        try {
            $impdetalle = array();

            date_default_timezone_set('America/Lima');
            $sdate = date("d") . "-" . date("m") . "-" . date("Y");

            $array = explode("↓↓↓", $datos);
            $importe_total = 0;
            for ($i = 0; $i < count($array); $i++) { 
                $detalles = explode("@@@", $array[$i]);

                $importe_total += (float) $detalles[3];

                $nomconcepto = Concepto::select('descripcion')->where('id', '=', $detalles[0])->get();
                $impdetalle[] = $detalles[4];
                $impdetalle[] = $nomconcepto[0]->descripcion . ' ' . $detalles[1]; 
                $impdetalle[] = $detalles[3];
            }

            $cliente = Cliente::select('nombres', 'apellidop', 'apellidom', 'nombre')
            ->join('escuelas', 'clientes.escuela_id', '=', 'escuelas.id')
            ->where('clientes.id', '=', $id_cliente)->get();

            $nombrecliente = utf8_encode($cliente[0]->nombres . ' ' . $cliente[0]->apellidop . ' ' . $cliente[0]->apellidom);
            $nombrescuela = utf8_encode($cliente[0]->nombre);

            //Aumentar en 1 la configuracion

            $secuencia = Config::find(1);
            $secuencia->sequence = (int) $numrecibo + 2;
            $secuencia->save();

            //Aumentar en 1 el ingreso que se reimprimió

            $ultimoingreso = Entry::select('id')->where('numrecibo', '=', $numrecibo)->get();
            $ingreso = Entry::find($ultimoingreso[0]->id);
            $ingreso->numrecibo = (String) ((int) ($numrecibo) + 1);
            $ingreso->save();

            if($nombrescuela != 'NO TIENE') {
                $this->impCompPago($nombrecliente, $nombrescuela, $importe_total, $impdetalle);
            }
            
            return response()->json([
                'alerta' => '<h2 style="color:blue; text-align: center">Se Modificó el Recibo Nº ' . $numrecibo . ' por el Nº ' . (String) ((integer) $numrecibo + 1) . '. </h2>',
                'numrecibo' => (String) ((integer) $numrecibo + 2),
            ]);
            
        } catch(\Exception $e) {
            return response()->json([
                'alerta' => 'No se pudo imprimir.',
            ]);
        }
    }

    public function detalles($id) {
        $detalles = Conceptos_Entries::select('conceptos__entries.cantidad', 'conceptos__entries.p_real', 'conceptos__entries.descripcion', 'conceptos__entries.importe', 'conceptos.descripcion as condescripcion')
        ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->where('entries.id', $id)->get();
        return response()->json([
            'alerta' => $detalles,
        ]);
    }

    public function anularrecibo($id, $cambio) {
        $detalles = Entry::find($id);

        $detalles->anulado = $cambio;
        $detalles->save();

        date_default_timezone_set('America/Lima');
        $sdate = date("d") . "-" . date("m") . "-" . date("Y");

        $total = Conceptos_Entries::select(DB::raw("SUM(importe) as ds"))
        ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->where('entries.numrecibo', 'NOT LIKE', '%;%')
        ->where('entries.fecha', '=', $sdate)
        ->where('entries.anulado', '=', true)->get();

        return response()->json([
            'total' => number_format($total[0]['ds'], 2, '.', ''),
        ]);
    }

    public function cargartasas($idconcepto){
        $cadena = '';
        $tasas = Tasa::select('id', 'descripcion', 'p_actual', 'igv')->where('concepto_id', $idconcepto)->get();

        $cadena .= '<option value="0">-- SELECCIONA TASA --</option>';

        foreach ($tasas as $tasa) {
            $cadena .= '<option value="' . $tasa->id . '@' . $tasa->p_actual . '@' . $tasa->igv . '@' . $tasa->descripcion . '">' . $tasa->descripcion . '</option>';
        }

        $cadena .= '<option value="N">-- NUEVA TASA --</option>';
            
        return response()->json([
            'cadena' => $cadena,
        ]);
    }

    public function cargarconceptos(){
        $cadena = '';
        $conceptos = Concepto::select('id', 'descripcion')->get();

        $cadena .= '<option value="0">-- SELECCIONA CONCEPTO --</option>';

        foreach ($conceptos as $concepto) {
            $cadena .= '<option value="' . $concepto->id . '">' . $concepto->descripcion . '</option>';
        }
            
        return response()->json([
            'cadena' => $cadena,
        ]);
    }

    protected function fetchCategoryTreeList($parent = 0, $user_tree_array = '') {

        if (!is_array($user_tree_array))
            $user_tree_array = array();

        $re_cc = CostCenter::select('id', 'name', 'parent_id', 'codigo', DB::raw('(select count(b.id) from cost_centers b where b.parent_id = cost_centers.id) as t'))
        ->where('parent_id', '=', $parent)
        ->orderby('id', 'ASC')
        ->get();

        if (count($re_cc) > 0) {
            $user_tree_array[] = "<ul>";

            foreach ($re_cc as $row) {
                if($row['t'] == 0) {
                    if($row['id'] != 1) {
                        $user_tree_array[] = "<li class='opcc'><a data-id='". $row['id']."' class='oplink' href='#". $row['name']."'>". $row['name']."</a></li>";
                    } else {
                        $user_tree_array[] = "<li><a href='#'>". $row['name']."</a></li>";
                    }
                } else {
                    if($row['id'] != 1) {
                        $user_tree_array[] = "<li class='opcc folder-root closed'><a data-id='". $row['id']."' class='oplink' href='#'>". $row['name']."</a>";
                    } else {
                        $user_tree_array[] = "<li class='folder-root closed'><a href='#'>". $row['name']."</a>";
                    }
                    $user_tree_array = $this->fetchCategoryTreeList($row['id'], $user_tree_array);
                }
            }

            $user_tree_array[] = "</li></ul>";
        } 
      return $user_tree_array;
    }

     public function porcobrar()
    {
        $clientes = Cliente::select('clientes.id', 'clientes.dni', 'clientes.codigo', 'clientes.nombres', 'clientes.apellidop', 'clientes.apellidom', 'clientes.tipo', 'escuelas.nombre as nombreescu', DB::raw('SUM(debs.deuda) AS total_deudas'), DB::raw('COUNT(debs.id) as cant_deudas'))
        ->join('escuelas', 'clientes.escuela_id', '=', 'escuelas.id')
        ->join('debs', 'debs.cliente_id', '=', 'clientes.id')
        ->where('debs.deuda', '<>', 0)
        ->groupby('clientes.id', 'clientes.codigo', 'clientes.nombres', 'clientes.apellidop', 'clientes.apellidom', 'escuelas.nombre', 'clientes.dni', 'clientes.tipo')
        ->orderby('clientes.id')
        ->paginate(15);
        return view('porcobrar.index', compact('clientes'));
    }

    public function regpapeletas($np1, $np2, $np3, $np4, $mp1, $mp2, $mp3, $mp4) {

        date_default_timezone_set('America/Lima');
        $sdate = date("d") . "-" . date("m") . "-" . date("Y");

        $res = SummarySheet::select('id')->where('fecha', '=', $sdate)->get();

        $resumen = SummarySheet::find((integer) $res[0]['id']);

        $total = Conceptos_Entries::select(DB::raw("SUM(importe) as ds"))
        ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->where('entries.fecha', $sdate)
        ->where('entries.anulado', 1)->get();

        $resumen->total = $total[0]['ds'];
        $resumen->estado = 1;

        if($mp1 != '0') {
            $resumen->np1 = $np1;
            $resumen->mp1 = $mp1;
        }
        if($mp2 != '0') {
            $resumen->np2 = $np2;
            $resumen->mp2 = $mp2;
        } 
        if($mp3 != '0') {
            $resumen->np3 = $np3;
            $resumen->mp3 = $mp3;
        }
        if($mp4 != '0') {
            $resumen->np4 = $np4;
            $resumen->mp4 = $mp4;
        }   

        if($resumen->save()) {
            return response()->json([
                'alerta' => 'YA CERRASTE CAJA HOY',
            ]);
        } else {
            return response()->json([
                'alerta' => 'No se Pudo cerrar Caja.',
            ]);
        }
    }

    public function regpapeletas_ant($fecha, $np1, $np2, $np3, $np4, $mp1, $mp2, $mp3, $mp4) {

        $res = SummarySheet::select('id')->where('fecha', '=', $fecha)->get();

        $resumen = SummarySheet::find((integer) $res[0]['id']);

        $total = Conceptos_Entries::select(DB::raw("SUM(importe) as ds"))
        ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
        ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
        ->where('entries.fecha', $fecha)
        ->where('entries.anulado', 1)->get();

        $resumen->total = $total[0]['ds'];
        $resumen->estado = 1;

        if($mp1 != '0') {
            $resumen->np1 = $np1;
            $resumen->mp1 = $mp1;
        }
        if($mp2 != '0') {
            $resumen->np2 = $np2;
            $resumen->mp2 = $mp2;
        } 
        if($mp3 != '0') {
            $resumen->np3 = $np3;
            $resumen->mp3 = $mp3;
        }
        if($mp4 != '0') {
            $resumen->np4 = $np4;
            $resumen->mp4 = $mp4;
        }   

        if($resumen->save()) {
            return response()->json([
                'alerta' => '<br>CAJA DEL DIA ' . $resumen->fecha . ' CERRADA CORRECTAMENTE',
            ]);
        } else {
            return response()->json([
                'alerta' => 'No se Pudo cerrar Caja.',
            ]);
        }
    }

    public function impCompPago($nombrecliente, $nombrescuela, $importe_total, $impdetalle) {
        $handle = printer_open("MATRICIAL");

        if($handle) {
            //ABRE LA IMPRESORA
            $lineas = 0;
            $cont = 1;
            
            printer_start_doc($handle, "Mi Documento");
            printer_start_page($handle);

            //LAS FUENTES (TIPO LETRA, ALTO, ANCHO, NEGRITA O NORMAL, CURSIVO, SUBRAYADO Y ORIENTACION)
            //ESCRIBIR RECIÉN (LINEA, LATITUD, LONGITUD)
            $font = printer_create_font("Franklin Gothic Medium", 12, 8, 50, false, false, false, 0);
            printer_select_font($handle, $font);

           
            printer_draw_text($handle, utf8_decode($nombrecliente) , 75, 120);
            printer_draw_text($handle, utf8_decode($nombrescuela), 680, 120);
            
            for ($i=0; $i < count($impdetalle); $i = $i + 3) { 

                $conc = utf8_decode($impdetalle[$i+1]); //260 - 650
                $conc1 = '';
                $conc2 = '';
                $conc3 = '';
                $conc4 = '';
                $cant_car = strlen($conc);

                $conc1 = substr($conc, 0, 45);
                if($cant_car >= 45) {
                    $conc2 = substr($conc, 45, 90);
                    if($cant_car - 90 >= 45) {
                        $conc3 = substr($conc, 90, 135);
                        if($cant_car - 135 >= 45) {
                            $conc4 = substr($conc, 135, $cant_car + 1);
                        }
                    }
                }

                printer_draw_text($handle, $impdetalle[$i], 90, 175 + ($cont * 15));
                printer_draw_text($handle, $conc1, 260, 175 + ($cont * 15));
                printer_draw_text($handle, $conc2, 260, 175 + ($cont * 15) + 15);
                printer_draw_text($handle, $conc3, 260, 175 + ($cont * 15) + 30);
                printer_draw_text($handle, $conc4, 260, 175 + ($cont * 15) + 45);

                printer_draw_text($handle, number_format(($impdetalle[$i+2] / $impdetalle[$i]), 2, '.', ','), 750, 175 + ($cont * 15)); 
                printer_draw_text($handle, number_format($impdetalle[$i+2], 2, '.', ','), 850, 175 + ($cont * 15));

                $lineas = 175 + ($cont * 15);
                $cont++;
            }

            printer_draw_text($handle, number_format($importe_total, 2, '.', ','), 850, 370);
            printer_draw_text($handle, date("Y-m-d"), 110, 370);
            printer_draw_text($handle, $this->numletras(number_format($importe_total, 2, '.', '')), 110, 385);

            //TERMINACIÓN DEL DOCUMENTO
            printer_delete_font($font);
            printer_end_page($handle);
            printer_end_doc($handle);
            printer_close($handle);
        } else {
            echo 'LA IMPRESORA ESTÁ DESCONECTADA';
        }   
    }

    public function nuevaTasa($descripcion, $p_actual, $igv, $id_centro_costos, $cantidad){
        $tasa = new Tasa;
        $tasa->descripcion = $descripcion;
        $tasa->p_actual = $p_actual/$cantidad;
        $tasa->igv = $igv;
        $tasa->concepto_id = $id_centro_costos;
        $tasa->save();
    }

    protected function numletras($numero) { 
        $tempnum = explode('.', $numero); 

        if ($tempnum[0] !== ""){ 
            $numf = $this->milmillon((int) $tempnum[0]); 
            if ($numf == "UNO") { 
                $numf = substr($numf, 0, -1); 
            } 
            $TextEnd = $numf.' CON ';
        } if ($tempnum[1] == "") { 
            $tempnum[1] = "00"; 
        } 
        $TextEnd .= $tempnum[1] ; 
        $TextEnd .= "/100 NUEVOS SOLES"; 
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

    protected function secuencia($sequence){
        $secuencia = Config::find(1);
        $secuencia->sequence = (int) $sequence;
        $secuencia->save();
        return response()->json([
            'sequence' => $sequence,
        ]);
    }

    public function prueba()
    {
       
       $ingreso_ant = Entry::select(DB::raw('max(id) as maximo'))->get();

        if(count($ingreso_ant) == 0){
            $maxidrecibo = 1;
        } else {
            $maxidrecibo = (integer) $ingreso_ant[0]->maximo;
        }            

        $num_rec_ant = Entry::select('numrecibo')->where('id', '=', $maxidrecibo)->get();

        if(count($num_rec_ant) == 0){
            $num_rec_ant1 = 1;
        } else {
            $num_rec_ant1 = (integer) $num_rec_ant[0]->numrecibo;
        } 

        echo $maxidrecibo;
    }

    protected function cadena_costcenter($id, $cadena = '') {

        $st1 = $this->st1($id);
        $st2 = $this->st2($id);
        return response()->json([
            'st1' => $st1,
            'st2' => $st2,
        ]);
    }

    protected function st1($id, $cadena = '') {

        $re_cc = CostCenter::select('id', 'name', 'parent_id')
        ->where('id', $id)
        ->get();

        if ($re_cc[0]->parent_id != 0) {
            $cadena .= $re_cc[0]->name . ' - ';    
            return $this->st1($re_cc[0]->parent_id, $cadena);     
        } 
        return substr($cadena, 0, strlen($cadena) - 3);
    }

    protected function st2($id, $cadena = '') {

        $re_cc = CostCenter::select('id', 'name', 'parent_id')
        ->where('id', $id)
        ->get();

        if ($re_cc[0]->parent_id != 0) {
            if ($re_cc[0]->id != 2 && $re_cc[0]->id != 3 && $re_cc[0]->id != 4) {
                $cadena .= $re_cc[0]->name . ' - ';    
                return $this->st2($re_cc[0]->parent_id, $cadena);  
            }
        } 
        return substr($cadena, 0, strlen($cadena) - 3);
    }
}