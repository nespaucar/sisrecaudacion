<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Input;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/prueba/{id}', 'CostCenterController@array_id');

Route::get('/prueba_funcion', 'GeneralController@prueba_funcion');

Route::get('/add_numserie_sum_sheet_hoy/{numserie}', 'GeneralController@add_numserie_sum_sheet_hoy');

//Clientes ------------------------------------------------------------------------
Route::resource('/cliente', 'ClienteController');

Route::get('/cargarClientes/{signo}/{archivo}', 'ClienteController@cargarClientes');

Route::get('/nuevocliente', 'ClienteController@registerCliente')->name('nuevocliente');

Route::get('/registrarcliente', 'ClienteController@store')->name('registrarcliente');

Route::get('cliente/{id}/destroy', [
	'uses' => 'ClienteController@destroy',
	'as' => 'cliente.destroy'
]);

Route::get('/buscarclientes', function() {
	/* Nuevo: si el argumento search está vacío regresar a la página anterior */
	if (empty(Input::get('search'))) return redirect()->back();
	$search = urlencode(e(Input::get('search')));
	$tipo = urlencode(e(Input::get('tipo')));
	$campo = urlencode(e(Input::get('campo')));
	$route = '/buscarclientes/' . $search . '/' . $tipo . '/' . $campo;
	return redirect($route);
})->name('buscarcliente');

Route::get('/buscarclientes/{search}/{tipo}/{campo}', 'ClienteController@search');

Route::get('/historialVentas/{id}', 'ClienteController@historialVentas');

//Facultades-----------------------------------------------------------------------
Route::resource('/facultad', 'FacultadController');

Route::get('/nuevafacultad', function(){
	return view('facultad.create');
})->name('nuevafacultad');

Route::post('/registrarfacultad', 'FacultadController@store')->name('registrarfacultad');

Route::get('facultad/{id}/destroy', [
	'uses' => 'FacultadController@destroy',
	'as' => 'facultad.destroy'
]);

Route::get('/buscarfacultades', function() {
	/* Nuevo: si el argumento search está vacío regresar a la página anterior */
	if (empty(Input::get('search'))) return redirect()->back();
	$search = urlencode(e(Input::get('search')));
	$campo = urlencode(e(Input::get('campo')));
	$route = '/buscarfacultades/' . $search . '/' . $campo;
	return redirect($route);
})->name('buscarfacultad');

Route::get('/buscarfacultades/{search}/{campo}', 'FacultadController@search');

//Personal-----------------------------------------------------------------------
Route::resource('/personal', 'PersonalController');

Route::get('/habUsuario/{id}/{cambio}', 'PersonalController@habUsuario');

Route::get('/noduplicidad/{elemento}', 'PersonalController@noduplicidad');

Route::get('/nuevoPersonal/{dni}/{tel}/{nom}/{app}/{apm}/{dir}/{email}/{tipo}/{user}', 'PersonalController@nuevoPersonal');

Route::get('/editarPersonal/{id}/{dni}/{tel}/{nom}/{app}/{apm}/{dir}/{email}/{tipo}/{user}', 'PersonalController@editarPersonal');

Route::get('/comprobarPassAnterior/{pass}/{name}', 'PersonalController@comprobarPassAnterior');

Route::get('/editarPass/{pass}/{name}', 'PersonalController@editarPass');

//Escuelas-----------------------------------------------------------------------
Route::resource('/escuela', 'EscuelaController');

Route::get('/nuevaescuela', 'EscuelaController@registerEscuela')->name('nuevaescuela');

Route::post('/registrarescuela', 'EscuelaController@store')->name('registrarescuela');

Route::get('escuela/{id}/destroy', [
	'uses' => 'EscuelaController@destroy',
	'as' => 'escuela.destroy'
]);

Route::get('/buscarescuelas', function() {
	/* Nuevo: si el argumento search está vacío regresar a la página anterior */
	if (empty(Input::get('search'))) return redirect()->back();
	$search = urlencode(e(Input::get('search')));
	$campo = urlencode(e(Input::get('campo')));
	$route = '/buscarescuelas/' . $search . '/' . $campo;
	return redirect($route);
})->name('buscarescuela');


Route::get('/buscarescuelas/{search}/{campo}', 'EscuelaController@search');

//Ingresos-----------------------------------------------------------------------
Route::get('/ingreso', 'IngresoController@index')->name('ingreso');

Route::get('/prueba', 'IngresoController@prueba')->name('prueba');

Route::get('/impCompPago', 'IngresoController@impCompPago');

Route::get('/centercosts', 'IngresoController@centercosts')->name('centercosts');

Route::get('datoscliente/{id}', 'IngresoController@buscarCliente')->name('datoscliente');

Route::get('/nuevoingreso', function() {
	$tipo = urlencode(e(Input::get('tipo')));
	$idtasa = urlencode(e(Input::get('idtasa')));
	$comprobante = urlencode(e(Input::get('comprobante')));
	if($comprobante != '0'){
		$numrecibo = urlencode(e(Input::get('nrovoucher'))) . ';' . urlencode(e(Input::get('nrocomprobante')));
	} else {
		$numrecibo = urlencode(e(Input::get('numrecibo')));
	}
	
	$cantidad_detalles = urlencode(e(Input::get('cant_detalles')));
	$id_cliente = urlencode(e(Input::get('id_cliente_oculto')));
	$id_cc = urlencode(e(Input::get('centro_costos')));
	date_default_timezone_set('America/Lima');
    $fecha = date("d") . "-" . date("m") . "-" . date("Y");
    $datos = '';
    $monto = 0;
    $total = 0;
    $estado = 0;
    $array = null;
    for ($i = 1; $i <= $cantidad_detalles; $i++) {
    	if($i == 1) {
    		$datos .= Input::get('inf_detalles' . $i); 
    	} else {
    		$datos .= '↓↓↓' . Input::get('inf_detalles' . $i); 
    	}
    	$dato = (string) Input::get('inf_detalles' . $i); 
    	$array = explode("@@@", $dato);
    	$monto = $monto + floatval($array[3]);
    	$total = $total + floatval($array[2]);
    	$dato = '';
    	$array = null;
	}
	if($monto == $total){
		$estado = 1;
	} else {
		$estado = 0;
	}
	$route = '/regdetalles/' . $numrecibo. '/' . $id_cliente . '/' . $fecha . '/' . $datos . '/' . $monto . '/' . $estado . '/' . $id_cc . '/' . $idtasa . '/' . $tipo;
	return redirect($route);
})->name('nuevoingreso');

Route::get('/regdetalles/{numrecibo}/{id_cliente}/{fecha}/{datos}/{monto}/{estado}/{id_cc}/{idtasa}/{tipo}', 'IngresoController@ingreso');

Route::get('/reimpNumReciboSig', function() {
	$numrecibo = (integer) urlencode(e(Input::get('numrecibo'))) - 1;
	$cantidad_detalles = urlencode(e(Input::get('cant_detalles')));
	$id_cliente = urlencode(e(Input::get('id_cliente_oculto')));
    $datos = '';
    $array = null;
    for ($i = 1; $i <= $cantidad_detalles; $i++) {
    	if($i == 1) {
    		$datos .= Input::get('inf_detalles' . $i); 
    	} else {
    		$datos .= '↓↓↓' . Input::get('inf_detalles' . $i); 
    	}
    	$dato = (string) Input::get('inf_detalles' . $i); 
    	$array = explode("@@@", $dato);
    	$dato = '';
    	$array = null;
	}
	$route = '/reimprimir/' . $numrecibo . '/' . $id_cliente . '/' . $datos;
	return redirect($route);
})->name('reimpNumReciboSig');

Route::get('/reimprimir/{numrecibo}/{id_cliente}/{datos}', 'IngresoController@reimprimir');

Route::get('/ventas_hoy', 'IngresoController@ventas_hoy')->name('ventas_hoy');

Route::get('/detalles/{id}', 'IngresoController@detalles');

Route::get('/impCompPago', 'IngresoController@impCompPago');

Route::get('/secuencia/{sequence}', 'IngresoController@secuencia');

Route::get('ingreso1', 'IngresoController@ingreso1');

Route::get('cadena_costcenter/{id}/{cadena}', 'IngresoController@cadena_costcenter');

Route::get('imp2_ingreso/{cadena}', 'IngresoController@imp2_ingreso');

Route::get('cargartasas/{idconcepto}', 'IngresoController@cargartasas');

Route::get('cargarconceptos', 'IngresoController@cargarconceptos');

Route::get('nuevaTasa/{descripcion}/{p_actual}/{igv}/{id_centro_costos}/{cantidad}', 'IngresoController@nuevaTasa');

//Conceptos-----------------------------------------------------------------------
Route::resource('/concepto', 'ConceptoController');

Route::get('/buscarconceptos', function() {
	/* Nuevo: si el argumento search está vacío regresar a la página anterior */
	if (empty(Input::get('search'))) return redirect()->back();
	$search = urlencode(e(Input::get('search')));
	$campo = urlencode(e(Input::get('campo')));
	$route = '/buscarconceptos/' . $search . '/' . $campo;
	return redirect($route);
})->name('buscarconceptos');

Route::get('/buscarconceptos/{search}/{campo}', 'ConceptoController@search');

Route::get('/nuevoconcepto', 'ConceptoController@registerConcepto')->name('nuevoconcepto');

Route::post('/registrarconcepto', 'ConceptoController@store')->name('registrarconcepto');

Route::get('/editarconcepto/{id}', 'ConceptoController@edit')->name('editarconcepto');

Route::get('concepto/{id}/destroy', [
	'uses' => 'ConceptoController@destroy',
	'as' => 'concepto.destroy'
]);

//Reportes---------------------------------------------------------------------------------------------------------

Route::get('/reportes', 'Reportes@index')->name('reportes');

Route::get('/repDiario/{tipo}/{fecha}/{fecha2}/{ordenado}/{incluir}', 'Reportes@CrearReporte')->name('repDiario');

Route::get('/repMensual/{tipo}/{fecha}/{fecha2}/{ordenado}/{incluir}', 'Reportes@CrearReporteM')->name('repMensual');

Route::get('/repAnual/{tipo}/{fecha}/{fecha2}/{ordenado}/{incluir}', 'Reportes@CrearReporteA')->name('repAnual');

Route::get('/repConcCentroCosto/{tipo}/{fecha}/{fecha2}/{id1}/{id2}', 'Reportes@CrearReporteCCC')->name('repCCC');

Route::get('/fecha', 'Reportes@descriciondefecha')->name('fecha');

Route::get('/repTerceros/{tipo}/{fecha1}/{fecha2}/{ordenado}/{modo}', 'Reportes@CrearReporteTerceros')->name('repTerceros');

//Recibo-----------------------------------------------------------------------------------------------------------
Route::get('/anularrecibo/{id}/{cambio}', 'IngresoController@anularrecibo');

Route::get('/regpapeletas/{np1}/{np2}/{np3}/{np4}/{mp1}/{mp2}/{mp3}/{mp4}', 'IngresoController@regpapeletas')->name('regpapeletas');

Route::get('/regpapeletas_ant/{fecha}/{np1}/{np2}/{np3}/{np4}/{mp1}/{mp2}/{mp3}/{mp4}', 'IngresoController@regpapeletas_ant')->name('regpapeletas_ant');

//Deudas-----------------------------------------------------------------------------------------------------------
Route::get('/porcobrar', 'IngresoController@porcobrar')->name('porcobrar');

Route::get('/historialDeudas/{id}', 'ClienteController@historialDeudas');

Route::get('/abonarDeuda/{recibo}/{abono}', 'ClienteController@abonarDeuda');

//Centro de Costos-------------------------------------------------------------------------------------------------
Route::get('/centro_de_costos', 'CostCenterController@index')->name('centro_de_costos');

Route::get('/nuevocentercost/{codigo}/{nombre}/{parent_id}', 'CostCenterController@nuevo')->name('nuevocentercost');

Route::get('/editarcentercost/{id}/{codigo}/{nombre}/{parent_id}', 'CostCenterController@edit')->name('editarcentercost');

//Generales-------------------------------------------------------------------------------------------------------
Route::get('/eliminar1/{id}/{table}', 'GeneralController@eliminar1')->name('eliminar1');

Route::get('/eliminar2/{id}/{table}', 'GeneralController@eliminar2')->name('eliminar2');

Route::get('/numletras/{num}', 'IngresoController@numletras');
