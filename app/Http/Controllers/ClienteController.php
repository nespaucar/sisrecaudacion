<?php 

namespace App\Http\Controllers;

use \App\Cliente;
use \App\Escuela;
use \App\Deb;
use \App\Conceptos_Entries;
use \App\Entry;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\DB;


class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clientes = Cliente::select('clientes.id', 'clientes.nombres', 'clientes.apellidop', 'clientes.apellidom', 'clientes.dni', 'clientes.codigo', 'clientes.tipo', 'escuelas.nombre as nombreescu')
        ->join('escuelas', 'clientes.escuela_id', '=', 'escuelas.id')
        ->orderby('clientes.codigo', 'desc')
        ->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    function registerCliente() {
        $escuelas = Escuela::select('escuelas.id', 'escuelas.nombre')->get();
        return view('clientes.create')->with('escuelas', $escuelas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, SessionManager $sessionManager)
    {
        //metodo para que ya no se ejecute nada más
        //dd($request);
        //Validación de datos que ingresa el usuario
        $this->validate($request, [
            'codigo' =>'string|max:10|unique:clientes',
            'nombres' => 'required|string|max:200',
            'apellidop' =>'required|string|max:50',
            'apellidom' => 'required|string|max:50',
            'dni' =>'required|string|max:11|unique:clientes',
        ]);

        try {
            //Instanciar el modelo cliente (Almacenamiento)
            $cliente = new Cliente;
            //Setear
            $cliente->codigo = $request->codigo;
            $cliente->nombres = $request->nombres;
            if($request->apellidop == '-'){
                $cliente->apellidop = '';
            } else {
                $cliente->apellidop = $request->apellidop;
            }
            if($request->apellidom == '-'){
                $cliente->apellidom = '';
            } else {
                $cliente->apellidom = $request->apellidom;
            }
            
            $cliente->tipo = $request->tipo;
            if($request->tipo == '1'){
                $cliente->escuela_id = $request->escuela_id;
            } else {
                $escuela = Escuela::select('id')->where('nombre', '=', 'NO TIENE')->get();
                $cliente->escuela_id = $escuela[0]['id'];
            }
            
            $cliente->dni = $request->dni;

            if($cliente->save()) {
                $message = 'Cliente con DNI ' . $cliente->dni . ' registrado correctamente';
            } else {
                $message = 'Ocurrió un error al registrar.';
            }

        } catch(\Exception $e) {
            $message = 'Ocurrió un error al registrar.';
        }

        $sessionManager->flash('alerta_create', $message);
        $escuelas = \App\Escuela::get();
        return view('clientes.create')->with('escuelas', $escuelas);
    }

    public function search($search, $tipo, $campo){
        $search = urldecode($search);
        $tipo = urldecode($tipo);
        $campo = urldecode($campo);

        if($campo != '0') {
            if($tipo != '0') {
                if($campo == 'nombres') {
                    $clientes = Cliente::select('clientes.id', 'clientes.nombres', 'clientes.apellidop', 'clientes.apellidom', 'clientes.dni', 'clientes.codigo', 'clientes.tipo', 'escuelas.nombre as nombreescu')
                    ->join('escuelas', 'clientes.escuela_id', '=', 'escuelas.id')
                    ->where(DB::raw("CONCAT(clientes.nombres, ' ', apellidop, ' ', apellidom)"), 'ILIKE', '%'.$search.'%', 'AND')
                    ->where('tipo', $tipo)
                    ->orderBy('dni', 'desc')
                    ->get();
                } else {
                    $clientes = Cliente::select('clientes.id', 'clientes.nombres', 'clientes.apellidop', 'clientes.apellidom', 'clientes.dni', 'clientes.codigo', 'clientes.tipo', 'escuelas.nombre as nombreescu')
                    ->join('escuelas', 'clientes.escuela_id', '=', 'escuelas.id')
                    ->where('clientes.' . $campo, 'ILIKE', '%'.$search.'%', 'AND')
                    ->where('tipo', $tipo)
                    ->orderBy('dni', 'desc')
                    ->get();
                }
            } else {
                if($campo == 'nombres') {
                    $clientes = Cliente::select('clientes.*', 'escuelas.nombre as nombreescu')
                    ->join('escuelas', 'clientes.escuela_id', '=', 'escuelas.id')
                    ->where(DB::raw("CONCAT(nombres, ' ', apellidop, ' ', apellidom)"), 'ILIKE', '%'.$search.'%')
                    ->orderBy('dni', 'desc')
                    ->get();
                } else {
                    $clientes = Cliente::where('clientes.' . $campo, 'ILIKE', '%'.$search.'%')
                    ->orderBy('dni', 'desc')
                    ->get();
                }

            }
                
        } else {
            if($tipo != '0') {
                $clientes = Cliente::select('clientes.*', 'escuelas.nombre as nombreescu')
                ->join('escuelas', 'clientes.escuela_id', '=', 'escuelas.id')
                ->where('tipo', $tipo)
                ->where(DB::raw("CONCAT(nombres, ' ', apellidop, ' ', apellidom, ' ', clientes.codigo, ' ', dni)"), 'ILIKE', '%'.$search.'%')
                ->orderBy('dni', 'desc')
                ->get();
            } else {
                $clientes = Cliente::select('clientes.*', 'escuelas.nombre as nombreescu')
                ->join('escuelas', 'clientes.escuela_id', '=', 'escuelas.id')
                ->where(DB::raw("CONCAT(nombres, ' ', apellidop, ' ', apellidom, ' ', clientes.codigo, ' ', dni)"), 'ILIKE', '%'.$search.'%')
                ->orderBy('dni', 'desc')
                ->get();                
            }
        }

        if($tipo == '0') {
            $tipo = 'Todos los Tipos';
        } else if($tipo == '1') {
            $tipo = 'Alumnos Internos';
        } else if($tipo == '2') {
            $tipo = 'Alumnos Foráneos';
        } else if($tipo == '3') {
            $tipo = 'Empresas';
        } else {
            $tipo = 'Otros';
        }

        if($campo == '0') {
            $campo = 'Todos los Campos';
        }

        if (count($clientes) == 0){
            return view('clientes.busqueda', compact('clientes'))
            ->with('search', '"' . $search . '" / Tipo: "' . $tipo . '" / Campo: "' . $campo . '" / Cantidad: "0"');
        } else {
            $filas = count($clientes);
            return view('clientes.busqueda', compact('clientes'))
            ->with('clientes', $clientes)
            ->with('search', '"' . $search . '" / Tipo: "' . $tipo . '" / Campo: "' . $campo . '" / Cantidad: "' . $filas . '"');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cliente = Cliente::find($id);
        $escuelas = Escuela::select('escuelas.id', 'escuelas.nombre')->get();
        return view('clientes.edit')->with('cliente', $cliente)->with('escuelas', $escuelas);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SessionManager $sessionManager, $id)
    {
        $this->validate($request, [
            'nombres' => 'required|string|max:200',
            'apellidop' =>'required|string|max:50',
            'apellidom' => 'required|string|max:50',
        ]);
        try {
            $cliente = Cliente::find((integer) $id);
            $cliente->codigo= $request->codigo;
            $cliente->dni = $request->dni;
            $cliente->tipo = $request->tipo;
            if($request->tipo == '1'){
                $cliente->escuela_id = $request->escuela_id;
            } else {
                $escuela = Escuela::select('id')->where('nombre', '=', 'NO TIENE')->get();
                $cliente->escuela_id = $escuela[0]['id'];
            }
            $cliente->nombres = $request->nombres;
            if($request->apellidop == '-'){
                $cliente->apellidop = '';
            } else {
                $cliente->apellidop = $request->apellidop;
            }
            if($request->apellidom == '-'){
                $cliente->apellidom = '';
            } else {
                $cliente->apellidom = $request->apellidom;
            }
            $cliente->save();
            if($cliente->save()) {
                $message = 'Cliente con DNI ' . $cliente->dni . ' editado correctamente';
            } else {
                $message = 'Ocurrió un error al editar.';
            }
        } catch (Exception $e) {
            $message = 'Ocurrió un error al editar.';
        }

        $sessionManager->flash('alerta_edit', $message);
        $escuelas = \App\Escuela::get();
        return view('clientes.edit')->with('escuelas', $escuelas)->with('cliente', $cliente);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */

    public function historialVentas($id) {
        $recibos = Entry::select('entries.id', 'entries.fecha', DB::raw("substring(numrecibo from position(';' in numrecibo) + 1 for char_length(numrecibo)) as numrecibo"), 'entries.monto', 'entries.anulado', 'entries.estado')
        ->join('clientes', 'clientes.id', '=', 'entries.cliente_id')
        ->where('entries.cliente_id', $id)->get();

        $detalles = array();
        for ($i=0; $i < count($recibos); $i++) { 
            $detalle = array();
            $detalle = Conceptos_Entries::select('conceptos__entries.id', 'conceptos__entries.cantidad', 'conceptos.descripcion', 'conceptos__entries.descripcion as dp', 'conceptos__entries.p_real', 'conceptos__entries.importe')
            ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->join('clientes', 'clientes.id', '=', 'entries.cliente_id')
            ->where('entries.id', $recibos[$i]->id)->get();

            $detalles[$i] = $detalle;
        }
        return response()->json([
            'recibos' => $recibos,
            'detalles' => $detalles,
        ]);
    }

    public function historialDeudas($id) {
        $recibos = Entry::select('entries.id', 'entries.fecha', 'entries.numrecibo', 'entries.monto', 'entries.anulado', 'entries.estado')
        ->join('clientes', 'clientes.id', '=', 'entries.cliente_id')
        ->where('entries.cliente_id', $id)
        ->where('entries.estado', 0)
        ->where('entries.anulado', 1)
        ->orderby('entries.numrecibo')
        ->get();

        $deudas = Deb::select('deuda')->where('deuda', '<>', 0)->where('cliente_id', $id)->orderby('numrecibo')->get();

        $detalles = array();
        for ($i=0; $i < count($recibos); $i++) { 
            $detalle = array();
            $detalle = Conceptos_Entries::select('conceptos__entries.id', 'conceptos__entries.cantidad', 'conceptos.descripcion', 'conceptos__entries.descripcion as dp', 'conceptos__entries.p_real', 'conceptos__entries.importe')
            ->join('entries', 'entries.id', '=', 'conceptos__entries.entry_id')
            ->join('conceptos', 'conceptos.id', '=', 'conceptos__entries.concepto_id')
            ->join('clientes', 'clientes.id', '=', 'entries.cliente_id')
            ->where('entries.id', $recibos[$i]->id)
            ->orderby('entries.numrecibo')->get();

            $detalles[$i] = $detalle;
        }
        return response()->json([
            'recibos' => $recibos,
            'detalles' => $detalles,
            'deudas' => $deudas,
        ]);
    }

    public function abonarDeuda($recibo, $abono) {
        $deuda = Deb::select('id', 'total', 'deuda')->where('numrecibo', $recibo)->get();
        $deuda2 = Deb::find((integer) $deuda[0]['id']);

        $deuda2->deuda = (float) $deuda[0]['deuda'] - (float) $abono;

        if($deuda2->save()) {
            $cantidad = 0;
            if($deuda2->deuda == 0) {
                $cantidad = -1;
                $recibo = Entry::select('id')->where('numrecibo', $recibo)->get();
                $recibo2 = Entry::find((integer) $recibo[0]['id']);

                $recibo2->estado = 1;
                $recibo2->save();
            }
            return response()->json([
                'abono' => $abono,
                'deuda' => $deuda2->deuda,
                'cantidad' => $cantidad,
            ]);
        } 
    }

    public function cargarClientes($separador, $archivo) {
        $errados = 0;
        $correctos = 0;
        if(($handle = fopen("C:/Users/". $archivo, 'r')) != false) {
            while (($data = fgetcsv($handle, 5000, $separador)) != false) {
                $cliente = new Cliente();
                $cliente->codigo = utf8_encode($data[0]);
                $cliente->nombres = utf8_encode($data[1]);
                $cliente->apellidop = utf8_encode($data[2]);
                $cliente->apellidom = utf8_encode($data[3]);
                $cliente->tipo = utf8_encode($data[4]);
                $cliente->dni = utf8_encode($data[5]);
                $cliente->escuela_id = $data[6];
                if($cliente->save()) {
                    $correctos++;
                } else {
                    $errados++;
                }
            }
            echo 'TOTAL: ' . $correctos . ' CORRECTOS Y ' . $errados . ' ERRADOS.';
        } else {
            echo 'HUBO UN PROBLEMA';
        }
        
    }
}
