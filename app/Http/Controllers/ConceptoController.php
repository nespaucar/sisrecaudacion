<?php

namespace App\Http\Controllers;

use \App\Concepto;
use \App\FinancialClassifier;
use \App\BudgetClassifier;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\DB;

class ConceptoController extends Controller
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
        $conceptos = Concepto::select('conceptos.id', 'conceptos.descripcion', 'financialclassifiers.codigo as fc', 'budgetclassifiers.codigo as bc')
        ->join('financialclassifiers', 'conceptos.financialclassifier_id', '=', 'financialclassifiers.id')
        ->join('budgetclassifiers', 'conceptos.budgetclassifier_id', '=', 'budgetclassifiers.id')
        ->orderby('financialclassifiers.codigo')
        ->paginate(15);
        return view('concepto.index', compact('conceptos'));
    }

    function registerConcepto() {
        $cfs = FinancialClassifier::select('financialclassifiers.id', 'financialclassifiers.codigo')->get();
        $cps = BudgetClassifier::select('budgetclassifiers.id', 'budgetclassifiers.codigo')->get();
        return view('concepto.create')->with('cfs', $cfs)->with('cps', $cps);
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
            'descripcion' =>'required|string|max:150',
            'cfn' =>'required|string|max:20',
            'cpn' =>'required|string|max:20',
        ]);

        try {
            //Instanciar el modelo concepto (Almacenamiento)
            $concepto = new Concepto;
            //Setear
            if((string) $request->cfn != '0') {
                $fc = new FinancialClassifier;
                $fc->codigo = $request->cfn;
                $fc->save();
                $concepto->financialclassifier_id = $fc->id;
            } else {
                $concepto->financialclassifier_id = (integer) $request->financialclassifier_id;
            }

            if((string) $request->cpn != '0') {
                $bc = new BudgetClassifier;
                $bc->codigo = $request->cpn;
                $bc->save();
                $concepto->budgetclassifier_id = $bc->id;
            } else {
                $concepto->budgetclassifier_id = (integer) $request->budgetclassifier_id;
            }

            $concepto->descripcion = $request->descripcion;
            
            if($concepto->save()) {
                $message = 'Concepto ' . $concepto->descripcion . ' registrado correctamente';
            } else {
                $message = 'Ocurrió un error al registrar.';
            }

        } catch(\Exception $e) {
            $message = 'Ocurrió un error al registrar.' . $request->cfn . $request->cpn;
        }

        $sessionManager->flash('alerta_create', $message);
        $cfs = FinancialClassifier::select('financialclassifiers.id', 'financialclassifiers.codigo')->orderby('codigo')->get();
        $cps = BudgetClassifier::select('budgetclassifiers.id', 'budgetclassifiers.codigo')->orderby('codigo')->get();
        return view('concepto.create', compact('concepto'))->with('cfs', $cfs)->with('cps', $cps);
    }

    /**
     * Display the specified resource.
     *
     * @param  Concepto  $concepto
     * @return \Illuminate\Http\Response
     */
    //public function show(Concepto $concepto)
    //{
    //    return view('concepto.edit', compact('concepto'));
    //}

    public function search($search, $campo){
        $search = urldecode($search);
        $campo = urldecode($campo);

        if($campo != '0') {
            $conceptos = Concepto::select('conceptos.*', 'financialclassifiers.codigo as fc', 'budgetclassifiers.codigo as bc')
            ->join('financialclassifiers', 'conceptos.financialclassifier_id', '=', 'financialclassifiers.id')
            ->join('budgetclassifiers', 'conceptos.budgetclassifier_id', '=', 'budgetclassifiers.id')
            ->where($campo, 'ILIKE', '%'.$search.'%')
            ->orderby('financialclassifiers.codigo')
            ->get();
            
        } else {
            $conceptos = Concepto::select('conceptos.*', 'financialclassifiers.codigo as fc', 'budgetclassifiers.codigo as bc')
            ->join('financialclassifiers', 'conceptos.financialclassifier_id', '=', 'financialclassifiers.id')
            ->join('budgetclassifiers', 'conceptos.budgetclassifier_id', '=', 'budgetclassifiers.id')
            ->where(DB::raw("CONCAT(conceptos.descripcion, ' ', financialclassifiers.codigo, ' ', budgetclassifiers.codigo)"), 'ILIKE', '%'.$search.'%')
            ->orderby('financialclassifiers.codigo')
            ->get();
        }

        if($campo == '0') {
            $campo = 'Todos los Campos';
        }

        if (count($conceptos) == 0){
            return view('concepto.busqueda', compact('conceptos'))
            ->with('search', '"' . $search . '" / Campo: "' . $campo . '" / Cantidad: "0"');
        } else {
            $filas = count($conceptos);
            return view('concepto.busqueda', compact('conceptos'))
            ->with('conceptos', $conceptos)
            ->with('search', '"' . $search . '" / Campo: "' . $campo . '" / Cantidad: "' . $filas . '"');
        }
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Concepto  $concepto
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $concepto = Concepto::find($id);
        $cfs = FinancialClassifier::select('financialclassifiers.id', 'financialclassifiers.codigo')->orderby('codigo')->get();
        $cps = BudgetClassifier::select('budgetclassifiers.id', 'budgetclassifiers.codigo')->orderby('codigo')->get();
        return view('concepto.edit')->with('concepto', $concepto)->with('cfs', $cfs)->with('cps', $cps);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Concepto  $concepto
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, SessionManager $sessionManager, $id)
    {
        $this->validate($request, [
            'descripcion' =>'required|string|max:150',
            'cfn' =>'required|string|max:20',
            'cpn' =>'required|string|max:20',
        ]);

        try {
            $concepto = Concepto::find((integer) $id);
            $concepto->descripcion= $request->descripcion;

            if((string) $request->cfn != '0') {
                $fc = new FinancialClassifier;
                $fc->codigo = $request->cfn;
                $fc->save();
                $concepto->financialclassifier_id = $fc->id;
            } else {
                $concepto->financialclassifier_id = (integer) $request->financialclassifier_id;
            }

            if((string) $request->cpn != '0') {
                $bc = new BudgetClassifier;
                $bc->codigo = $request->cpn;
                $bc->save();
                $concepto->budgetclassifier_id = $bc->id;
            } else {
                $concepto->budgetclassifier_id = (integer) $request->budgetclassifier_id;
            }

            if($concepto->save()) {
                $message = 'Concepto ' . $concepto->descripcion . ' editado correctamente';
            } else {
                $message = 'Ocurrió un error al editar.';
            }
        } catch (Exception $e) {
            $message = 'Ocurrió un error al editar.';
        }

        $sessionManager->flash('alerta_edit', $message);
        $cfs = FinancialClassifier::select('financialclassifiers.id', 'financialclassifiers.codigo')->get();
        $cps = BudgetClassifier::select('budgetclassifiers.id', 'budgetclassifiers.codigo')->get();
        return view('concepto.edit')->with('concepto', $concepto)->with('cfs', $cfs)->with('cps', $cps);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Concepto  $concepto
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, SessionManager $sessionManager)
    {
        try {
            $concepto = Concepto::find($id);
            $concepto->delete();
            if(!$concepto->delete()) {
                $message = 'Concepto ' . $concepto->descripcion . ' eliminado correctamente';
            } else {
                $message = 'Ocurrió un error al eliminar.';
            }
        } catch(\Exception $e) {
            $message = 'Ocurrió un error al eliminar.';
        }
        

        $sessionManager->flash('alerta', $message);
        $concepto = Concepto::paginate(15);
        return redirect()->route('concepto.index');
    }
}