<?php

namespace App\Http\Controllers;
use App\CostCenter;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
	public function index()
    {

        $centercosts = $this->fetchCategoryTreeList();
        $padres = $this->padres();

        return view('costcenter.index', compact('centercosts', 'padres'));
    }

    public function nuevo($codigo, $nombre, $parent_id) {
        $id = CostCenter::select(DB::raw('max(id) as maxid'))->get();
        $costcenter = new CostCenter;
        $costcenter->codigo = 'FICSA-' . $id[0]->maxid;
        $costcenter->name = $nombre;
        $costcenter->parent_id = $parent_id;

        if($costcenter->save()){
            $centercosts = $this->fetchCategoryTreeList();
            $padres = $this->padres();

            return response()->json([
                'centercosts' => $centercosts,
                'padres' => $padres,
                'mensaje' => 'CenterCost insertado Correctamente',
            ]);
        }
    }

    public function edit($id, $codigo, $nombre, $parent_id) {
        $costcenter = CostCenter::find((integer) $id);
        $costcenter->codigo = $codigo;
        $costcenter->name = $nombre;
        $costcenter->parent_id = $parent_id;

        if($costcenter->save()){
            $centercosts = $this->fetchCategoryTreeList();
            $padres = $this->padres();

            return response()->json([
                'centercosts' => $centercosts,
                'padres' => $padres,
                'mensaje' => 'CenterCost insertado Correctamente',
            ]);
        }
    }

    public function eliminar1($id) {
        $costcenter = CostCenter::select(DB::raw('count(entries.id) as entradas'), DB::raw('sum(entries.monto) as monto'))
        ->join('entries', 'entries.costcenter_id', '=', 'cost_centers.id')
        ->where('cost_centers.id', '=', $id)
        ->get();

        if($costcenter[0]->entradas == 0){
            return response()->json([
                'mensaje' => 'NO TIENE ninguna venta identificada, si la elimina no afectará la integridad de su información.',
            ]);
        } else {
            return response()->json([
                'mensaje' => 'TIENE venta(s) identificada(s): <br> <b style="color:#42FF33">CANTIDAD DE VENTAS: ' . $costcenter[0]->entradas . '. <br> MONTO TOTAL: S/. ' . $costcenter[0]->monto . '.</b><br> <b style="color:#FF3371">La eliminación, causará pérdidas de información.</b>',
            ]);            
        }
    }

    public function eliminar2($id) {
        
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
                    $user_tree_array[] = "<li class='opcc ". $row['id']."'><a data-codigo= '". $row['codigo']."' data-id='". $row['id']."'  data-hijos='" . $row['t'] . "' data-padre='" . $row['parent_id'] . "' class='oplink' href='#". $row['name']."'>". $row['name']."</a></li>";
                } else {
                    $user_tree_array[] = "<li class='opcc folder-root closed ". $row['id']."'><a data-codigo= '". $row['codigo']."' data-id='". $row['id']."'  data-hijos='" . $row['t'] . "' data-padre='" . $row['parent_id'] . "' class='oplink' href='#'>". $row['name']."</a>";
                    $user_tree_array = $this->fetchCategoryTreeList($row['id'], $user_tree_array);
                }
            }

            $user_tree_array[] = "</li></ul>";
        } 
      return $user_tree_array;
    }

    protected function padres($parent = 0, $padres = '') {

        if (!is_array($padres))
            $padres = array();

        $re_cc = CostCenter::select('id', 'name', 'parent_id', 'codigo', DB::raw('(select count(b.id) from cost_centers b where b.parent_id = cost_centers.id) as t'))
        ->where('parent_id', '=', $parent)
        ->orderby('id', 'ASC')
        ->get();

        if (count($re_cc) > 0) {
            $padres[] = '';

            foreach ($re_cc as $row) {
                $padres[] = "<option id='". $row['id']."' class='ppp' value='". $row['id']."'>". $row['name']."</option>";
                $padres = $this->padres($row['id'], $padres);
            }
        } 
      return $padres;
    }
}
