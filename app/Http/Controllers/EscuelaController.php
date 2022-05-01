<?php

namespace App\Http\Controllers;

use App\Escuela;
use App\Facultad;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\DB;

class EscuelaController extends Controller
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
        $escuelas = Escuela::select('escuelas.id', 'escuelas.nombre', 'escuelas.codigo', 'facultads.nombre as nombrefacu')
        ->join('facultads', 'escuelas.facultad_id', '=', 'facultads.id')
        ->get();
        return view('escuela.index', compact('escuelas'));
    }

    function registerEscuela() {
        $facultades = Facultad::select('facultads.id', 'facultads.nombre')->get();
        $max = Escuela::select(DB::raw("LPAD((CAST((COUNT('id') + 1) AS TEXT)), 3, '0') as max"))->get();
        return view('escuela.create')->with('facultades', $facultades)->with('max', $max);
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
            'codigo' =>'required|string|max:6|unique:escuelas',
            'nombre' => 'required|string|max:90',
        ]);

        try {
            //Instanciar el modelo escuela (Almacenamiento)
            $escuela = new Escuela;
            //Setear
            $escuela->codigo = $request->codigo;
            $escuela->nombre = $request->nombre;
            $escuela->facultad_id = $request->facultad_id;
            if($escuela->save()) {
                $message = 'Escuela ' . $escuela->nombre . ' registrada correctamente';
            } else {
                $message = 'Ocurrió un error al registrar.';
            }

        } catch(\Exception $e) {
            $message = 'Ocurrió un error al registrar.';
        }

        $sessionManager->flash('alerta_create', $message);
        $facultades = \App\Facultad::get();
        $max = Escuela::select(DB::raw("LPAD((CAST((COUNT('id') + 1) AS TEXT)), 3, '0') as max"))->get();
        return view('escuela.create')->with('facultades', $facultades)->with('max', $max);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Escuela  $escuela
     * @return \Illuminate\Http\Response
     */
    //public function show(escuela $escuela)
    //{
    //    return view('escuela.edit', compact('escuela'));
    //}

    public function search($search, $campo){
        $search = urldecode($search);
        $campo = urldecode($campo);

        if($campo != '0') {
            $escuelas = Escuela::select('escuelas.id', 'escuelas.codigo', 'escuelas.nombre', 'facultads.nombre as nombrefacu')
            ->join('facultads', 'escuelas.facultad_id', '=', 'facultads.id')
            ->where('escuelas.' . $campo, 'ILIKE', '%'.$search.'%')
            ->orderBy('escuelas.codigo', 'desc')
            ->get();
            
        } else {
            $escuelas = Escuela::select('escuelas.*', 'facultads.nombre as nombrefacu')
            ->join('facultads', 'escuelas.facultad_id', '=', 'facultads.id')
            ->where(DB::raw("CONCAT(escuelas.codigo, ' ', escuelas.nombre)"), 'ILIKE', '%'.$search.'%')
            ->orderBy('escuelas.codigo', 'desc')
            ->get();
        }

        if($campo == '0') {
            $campo = 'Todos los Campos';
        }

        if (count($escuelas) == 0){
            return view('escuela.busqueda', compact('escuelas'))
            ->with('search', '"' . $search . '" / Campo: "' . $campo . '" / Cantidad: "0"');
        } else {
            $filas = count($escuelas);
            return view('escuela.busqueda', compact('escuelas'))
            ->with('escuelas', $escuelas)
            ->with('search', '"' . $search . '" / Campo: "' . $campo . '" / Cantidad: "' . $filas . '"');
        }
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Escuela  $escuela
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $escuela = Escuela::find($id);
        $facultades = Facultad::select('facultads.id', 'facultads.nombre')->get();
        return view('escuela.edit')->with('escuela', $escuela)->with('facultades', $facultades);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Escuela  $escuela
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SessionManager $sessionManager, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:90',
        ]);
        try {
            $escuela = Escuela::find((integer) $id);
            $escuela->codigo= $request->codigo;
            $escuela->nombre = $request->nombre;
            $escuela->facultad_id = $request->facultad_id;
            $escuela->save();
            if($escuela->save()) {
                $message = 'Escuela ' . $escuela->nombre . ' editada correctamente';
            } else {
                $message = 'Ocurrió un error al editar.';
            }
        } catch (Exception $e) {
            $message = 'Ocurrió un error al editar.';
        }

        $sessionManager->flash('alerta_edit', $message);
        $facultades = \App\Facultad::get();
        return view('escuela.edit')->with('facultades', $facultades)->with('escuela', $escuela);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Escuela $escuela
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, SessionManager $sessionManager)
    {
        try {
            $escuela = Escuela::find($id);
            $escuela->delete();
            if(!$escuela->delete()) {
                $message = 'escuela ' . $escuela->nombre . ' eliminado correctamente';
            } else {
                $message = 'Ocurrió un error al eliminar.';
            }
        } catch(\Exception $e) {
            $message = 'Ocurrió un error al eliminar.';
        }
        

        $sessionManager->flash('alerta', $message);
        $escuela = Escuela::paginate(15);
        return redirect()->route('escuela.index');
    }
}

