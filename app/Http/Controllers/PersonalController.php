<?php

namespace App\Http\Controllers;

use App\Personal;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\DB;
use Auth;

class PersonalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $personal = Personal::select('users.id', 'dni', 'nombres', 'apellidop', 'apellidom', 'direccion', 'telefono', 'email', 'estado', 'type')->join('users', 'personals.id', '=', 'users.personal_id')->orderby('type', 'id', 'asc')->get();

        return view('personal.index', compact('personal'));
    }

    public function habUsuario($id, $cambio) {
    	$usuario = User::find($id);

        $usuario->estado = $cambio;
        $usuario->save();

        if($cambio == 1) {
        	$men = 'HABIL';
        } else {
        	$men = "INHAB";
        }
    }

    public function noduplicidad($elemento) {
        $existe = '0';
        if(!strpos($elemento, '@')) {
            $personal = Personal::where('dni', '=', $elemento)->get();
            if(count($personal) > 0) {
                $existe = '1';
            } 
        } else {
            $usuario = User::where('email', '=', $elemento)->get();
            if(count($usuario) > 0) {
                $existe = '1';
            }
        }
        
        return response()->json([
            'existe' => $existe,
        ]);
    }

    public function nuevoPersonal($dni, $tel, $nom, $app, $apm, $dir, $email, $tipo, $user) {
        $personal = new Personal;
        //Setear
        $personal->dni = $dni;
        $personal->nombres = $nom;
        $personal->apellidop = $app;
        $personal->apellidom = $apm;
        $personal->telefono = $tel;
        $personal->direccion = $dir;
        $btn = '';
        $nuevoregistro = '';
        $rol = '';

        if($personal->save()) {

            $usuario = new User;
            $usuario->name = $user;
            $usuario->email = $email;
            $usuario->password = bcrypt('admin');
            $usuario->estado = true;
            $usuario->type = $tipo;
            $usuario->personal_id = $personal->id;
            
            if($usuario->save()) {
                if($tipo == '1') {
                    $rol = 'SUPER ADMINISTRADOR';
                } else if($tipo == '2') {
                    $rol = 'ADMINISTRADOR';
                } else {
                    $rol = 'PERSONAL ORDINARIO';
                }

                $message = 'Se creó automáticamente el usuario "' . $usuario->name . '" para el trabajador "' . $personal->nombres . ' ' . $personal->apellidop . ' ' . $personal->apellidom . '".';
                if($tipo == '1') {
                    $btn = '<input id="btn' . $personal->id . '" type="button" class="btn btn-primary" value="HABIL">';
                } else {
                    $btn = '<input id="btn' . $personal->id . '" type="button" class="btn btn-success hab" data-type="' . $usuario->estado . '" data-id="' . $usuario->id . '" value="HABIL" onclick="hab(' . "'" . $usuario->id . "'" . ')">';
                }

                $nuevoregistro = '
                    <td>
                        <i class="icon-check"></i>  <font id="dni' . $personal->id . '">' . $personal->dni . '</font> <br> 
                        <i class="icon-check"></i>  <font id="nom' . $personal->id . '">' . $personal->nombres . '</font> <font id="app' . $personal->id . '"> ' . $personal->apellidop . ' </font> <font id="apm' . $personal->id . '"> ' . $personal->apellidom . '</font> <br> 
                        <i class="icon-check"></i>  <font id="dir' . $personal->id . '">' . $personal->direccion . '</font> <br> 
                        <i class="icon-check"></i>  <font id="tel' . $personal->id . '">' . $personal->telefono . '</font> <br> 
                        <i class="icon-check"></i>  <font id="email' . $personal->id . '">' . $usuario->email . '</font>
                    </td>
                    <td>' . $rol . '</td>

                    <td>' . $btn . '
                        <span class="label label-primary">Nuevo</span>
                    </td>
                    <td>
                        <a id="edtReg' . $personal->id . '" href="#" data-toggle="modal" data-target="#modalpersonal" data-id="' . $personal->id . '" data-tipo="' . $usuario->type . '" data-estado="1" class="btn btn-sm btn-info edtReg"><i class="icon-pencil text-center"></i>
                        </a>
                    </td>
                    <td>
                        <a data-bean="personal" data-nombre="' . $personal->nombres . ' ' . $personal->apellidop . ' ' . $personal->apellidom . '" data-id="' . $personal->id . '" data-table="al trabajador" data-toggle="modal" data-target="#deleteModal" class="eliminarBean btn btn-sm btn-warning">
                            <i class="icon-remove text-center"></i>
                        </a>
                    </td>';
            } else {
                $message = 'Usuario no Insertado - Persona si Insertada';
            }
        } else {
            $message = 'Personal y Usuario no Insertados';
        }

        return response()->json([
            'mensaje' => $message,
            'nuevoregistro' => $nuevoregistro,
            'id' => $personal->id,
        ]);

    }

    public function editarPersonal($id, $dni, $tel, $nom, $app, $apm, $dir, $email, $tipo, $user) {
        $personal = Personal::find((integer) $id);
        $personal->dni = $dni;
        $personal->nombres = $nom;
        $personal->apellidop = $app;
        $personal->apellidom = $apm;
        $personal->direccion = $dir;
        $personal->telefono = $tel;
        $mensaje = '';

        if($personal->save()) {
            $usuario = User::find((integer) $id);
            $usuario->name = $user;
            $usuario->email = $email;
            $usuario->password = bcrypt('admin');
            $usuario->type = $tipo;
            $usuario->personal_id = $personal->id;
            $usuario->estado = true;

            if($usuario->save()) {
                $mensaje = "El Trabajador con DNI '" . $personal->dni . "' ha sido editado correctamente";
            } else {
                $mensaje = "El cliente ha sido editado correctamente pero no se pudo editar el Usuario.";
            }
        } else {
            $mensaje = "No se pudieron editar ni el Trabajador ni el usuario.";
        } 

        return response()->json([
            'mensaje' => $mensaje,
            'id' => $personal->id,
            'dni' => $personal->dni,
            'nombres' => $personal->nombres,
            'apellidop' => $personal->apellidop,
            'apellidom' => $personal->apellidom,
            'direccion' => $personal->direccion,
            'telefono' => $personal->telefono,
            'email' => $usuario->email,
            'tipo' => $usuario->type,
            'estado' => $usuario->estado,
        ]);
    }

    public function comprobarPassAnterior($pass, $name) {
        $resultado = '0';
        $res = User::select('id', 'password')->where('name', '=', $name)->get();
        if(password_verify($pass, $res[0]->password)) {
            $resultado = '1';
        }
        return response()->json([
            'correcto' => $resultado,
        ]);
    }

    public function editarPass($pass, $name) {
        $mensaje = '';
        $usu = User::select('id')->where('name', '=', $name)->get();
        $usuario = User::find((integer) $usu[0]->id);
        $usuario->password = bcrypt($pass);
        if($usuario->save()) {
            $mensaje = 'Contraseña editada Correctamente.';
        } else {
            $mensaje = 'No se pudo cambiar Contraseña.';
        }

        return response()->json([
            'mensaje' => $mensaje,
        ]);
    }
}
