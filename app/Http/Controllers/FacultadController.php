<?php

namespace App\Http\Controllers;

use App\Facultad;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\DB;

class FacultadController extends Controller
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
        $facultades = Facultad::select('id', 'codigo', 'nombre')->get();
        return view('facultad.index', compact('facultades'));
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
            'codigo' =>'required|string|max:6|unique:facultads',
            'nombre' => 'required|string|max:90',
        ]);

        try {
            //Instanciar el modelo facultad (Almacenamiento)
            $facultad = new Facultad;
            //Setear
            $facultad->codigo = $request->codigo;
            $facultad->nombre = $request->nombre;
            if($facultad->save()) {
                $message = 'Facultad ' . $facultad->nombre . ' registrada correctamente';
            } else {
                $message = 'Ocurrió un error al registrar.';
            }

        } catch(\Exception $e) {
            $message = 'Ocurrió un error al registrar.';
        }

        $sessionManager->flash('alerta_create', $message);
        return view('facultad.create', compact('facultad'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Facultad  $facultad
     * @return \Illuminate\Http\Response
     */
    //public function show(Facultad $facultad)
    //{
    //    return view('facultad.edit', compact('facultad'));
    //}

    public function search($search, $campo){
        $search = urldecode($search);
        $campo = urldecode($campo);

        if($campo != '0') {
            $facultades = Facultad::where($campo, 'ILIKE', '%'.$search.'%')
            ->orderBy('codigo', 'desc')
            ->get();
            
        } else {
            $facultades = Facultad::where(DB::raw("CONCAT(codigo, ' ', nombre)"), 'ILIKE', '%'.$search.'%')
            ->orderBy('codigo', 'desc')
            ->get();
        }

        if($campo == '0') {
            $campo = 'Todos los Campos';
        }

        if (count($facultades) == 0){
            return view('facultad.busqueda', compact('facultades'))
            ->with('search', '"' . $search . '" / Campo: "' . $campo . '" / Cantidad: "0"');
        } else {
            $filas = count($facultades);
            return view('facultad.busqueda', compact('facultades'))
            ->with('facultades', $facultades)
            ->with('search', '"' . $search . '" / Campo: "' . $campo . '" / Cantidad: "' . $filas . '"');
        }
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Facultad  $facultad
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $facultad = Facultad::find($id);
        return view('facultad.edit')->with('facultad', $facultad);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Facultad  $facultad
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SessionManager $sessionManager, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:90',
        ]);
        try {
            $facultad = Facultad::find((integer) $id);

            $facultad->codigo= $request->codigo;
            $facultad->nombre = $request->nombre;
            $facultad->save();
            if($facultad->save()) {
                $message = 'Facultad ' . $facultad->nombre . ' editada correctamente';
            } else {
                $message = 'Ocurrió un error al editar.';
            }
        } catch (Exception $e) {
            $message = 'Ocurrió un error al editar.';
        }

        $sessionManager->flash('alerta_edit', $message);
        return view('facultad.edit')->with('facultad', $facultad);
    }
}
