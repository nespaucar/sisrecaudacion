@extends('layouts.app')

@section('content')
<script>
  $(document).ready(function() {
    $('.carousel-indicators').hide();
    $('#impClientes').modal({
      show: false,
      backdrop: 'static', 
      keyboard: true
    });
    $('#carouselSubirArchivo').carousel({
      interval: 0
    });
  });

  $(document).on('click', '#changePass', function() {
    var passAnt = $('#passAnt').val();
    var passNue = $('#passNue').val();
    var passNue2 = $('#passNue2').val();

    if(passAnt == '') {
        $('.spn_form_personal').addClass('hide');
        $('#passAnt').focus();
        $('#spn_passant').removeClass('hide').html('Ingresa Contraseña Anterior.');
        return false;
    } 

    var passCorrecta = comprobarPassAnterior(passAnt, '{{ Auth::user()->name }}');
    if(passCorrecta.correcto != '1') {
        $('.spn_form_personal').addClass('hide');
        $('#spn_passant').removeClass('hide').html('No es tu contraseña actual.');
        $('#passAnt').focus();
        return false;
    }

    if(passNue == '' || passNue2 == '') {
        if(passNue == ''){ 
            $('#passNue').focus();
    
        } else {
            $('#passNue2').focus();
        }
        $('.spn_form_personal').addClass('hide').html('Ingresa Contraseña Nueva.');
        $('#spn_passnue').removeClass('hide');
        return false;
    }

    if(passNue != passNue2) {
        $('.spn_form_personal').addClass('hide');
        $('#passNue2').val('').focus();
        $('#spn_passnue').removeClass('hide').html('Las contraseñas no coinciden.');
        return false;
    }

    $.ajax({
      url: 'editarPass/' + passNue + '/' + '{{ Auth::user()->name }}',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      type: 'GET',
      dataType: 'json',
      beforeSend: function(){
        $("#cargando").html("<img src='{{ asset('img/cargando.gif') }}' width='22' height='22' />");
      },
      success: function(res) {
        $("#cargando").html('');
        $('#passAnt').val('');
        $('#passNue').val('');
        $('#passNue2').val('');
        $('.spn_form_personal').addClass('hide');
        $('#sms_changepass').removeClass('hide').attr('style', 'color:green').html(res.mensaje);
      }
    });
});

function comprobarPassAnterior(string, name){
    return JSON.parse($.ajax({
        url: 'comprobarPassAnterior/' + string + '/' + name,
        type: 'GET',
        async: false,
        dataType: 'json',
        success: function(result) {
            return result;
        }
    }).responseText);
}


$(document).on('submit', '#subida', function(){
  var comprobar = $('#csv').val().length;
  var extension = $('#csv').val().substring(comprobar - 4);
  var fic = $('#csv').val().split('\\');
  var nombre = fic[fic.length-1];
  if(comprobar != 0) {
    if(extension == '.csv') {
      $.ajax({
        url: 'cargarClientes/' + $('#separacion').val() + '/' + nombre,
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        type: 'GET',
        contentType: false,
        processData:false,
        beforeSend: function(){
          $("#spn_csv").removeClass('hide').html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
        },
        success: function(data){
          $('#spn_csv').removeClass('hide').html('<font color="green"><b>' + data + '</b></font>');
        }
      }).fail(function(){
        $('#spn_csv').removeClass('hide').html('<font color="red"><b>Ocurrió un error al Subir los datos.</b></font>');
      });
    } else {
      $('#spn_csv').removeClass('hide').html('<font color="red"><b>Solo archivos (.csv).</b></font>');
    }
  } else {
    $('#spn_csv').removeClass('hide').html('<font color="red"><b>Debes seleccionar un archivo.</b></font>');
  }
  return false;
});

$(document).on('click', '.modal', function(){
  $('#spn_csv').addClass('hide');
});


</script>

<div class="container">
    <div class="row">
        <div class="col-md-2 col-sm-1 hidden-sm hidden-xs">
          <div class="affix" style="width: 13%">
            <div class="panel panel-default">
              <div class="panel-heading">MENÚ</div>
                <div class="panel-body">
                    <ul class="nav nav-pills nav-stacked">
                      <li id="inicio"><a class="text-center" href="{{ route("login") }}"><i class="icon-home btn btn-danger"></i></a></li>
                      @if(Auth::user()->type == 1)
                      <li id="personal"><a href="{{ route("personal.index") }}"><i class="icon-user"></i> Personal</a></li>
                      <li id="facultades"><a href="{{ route("facultad.index") }}"><i class="icon-suitcase"></i> Facultades</a></li>
                      <li id="escuelas"><a href="{{ route("escuela.index") }}"><i class="icon-sitemap"></i> Escuelas</a></li>
                      @endif
                      <li id="clientes"><a href="{{ route("cliente.index") }}"><i class="icon-male"></i> Clientes</a></li>
                      <li id="ingresos"><a href="{{ route("ingreso") }}"><i class="icon-money"></i> Ingresos</a></li>
                      <li id="porcobrar"><a href="{{ route("porcobrar") }}"><i class="icon-tags"></i> Por Cobrar</a></li>
                      <li id="centro_de_costos"><a href="{{ route("centro_de_costos") }}"><i class="icon-briefcase"></i> CostCenter</a></li>
                      <li id="conceptos"><a href="{{ route("concepto.index") }}"><i class="icon-sort-by-attributes-alt"></i> Conceptos</a></li>
                      @if(Auth::user()->type == 1)
                      <li id="reportes"><a href="{{ route("reportes") }}"><i class="icon-file-text"></i> Reportes</a></li>
                      @endif
                      <li id="reportes"><a href="{{ route("ventas_hoy") }}" target="_blank"><i class="icon-dollar"></i> Ventas de Hoy</a></li>
                    </ul>
                </div>
            </div>
          </div>
        </div>
        <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading"><div id="titulo">Bienvenido(a) <b>{{ Auth::user()->email }}</b>, es un gusto tenerte nuevamente por aquí.</div></div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @yield('contentbody')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambio de Eliminar -->

<div id="deleteModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
      <!-- Modal content-->
      <div class="modal-content">
          <div class="modal-header">
            <div class="row">
              <div class="col-md-9">
                <h4 class="modal-title"><b style="color:red;"><i class="icon-warning-sign"></i> Eliminar??</b></h4>
              </div>
            </div>
          </div>
          <div class="modal-body" id="mens_elim"></div>
          <div class="modal-body" id="mens_alerta">
            <h4 class="modal-title"><b style="color:blue;">Estás seguro que deseas eliminar <b id="table"></b>: <br> <b style="color:green" id="nombre_cc"></b>? <hr> <b id="mensaje" style="color:orange"></b></b></h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            <button id="elimina2" type="button" class="btn btn-success">Aceptar</button>
          </div>
      </div>
    </div>
</div>

<!-- Modal Cambio de Contraseña -->

<div id="changePassModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
      <!-- Modal content-->
      <div class="modal-content">
          <div class="modal-header">
              <div class="col-md-12">
                <button type="button" class="close"><font id="cargando"></font></button>
                <h5 class="modal-title"><b style="color:green;"><i class="icon-edit"></i> Cambio de Contraseña</b></h5>
            </div>
          </div>
          <div class="modal-body" id="mens_elim">
            <form role="form">
              <div class="form-group">
                <label for="passAnt label-sm">Contraseña Anterior</label>
                <input type="password" class="form-control input-sm" id="passAnt"
                    placeholder="Contraseña Anterior" autofocus="" autocomplete="on">
                <span class="spn_form_personal" id="spn_passant">Ingresa Contraseña Anterior.</span>
              </div>
              <div class="form-group">
                <label for="passNue label-sm">Contraseña Nueva</label>
                <input type="password" class="form-control input-sm" id="passNue"
                    placeholder="Contraseña Nueva">
                <span class="spn_form_personal" id="spn_passnue"></span>
              </div>
              <div class="form-group">
                <label for="passNue2 label-sm">Repite</label>
                <input type="password" class="form-control input-sm" id="passNue2"
                    placeholder="Contraseña Nueva">
              </div>
              <div class="form-group">
                <div class="col-md-12 text-center">
                  <font class="text-center" id="sms_changepass"></font>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            <button id="changePass" type="button" class="btn btn-success">Aceptar</button>
          </div>
      </div>
    </div>
</div>

<!-- Modal Importación de Clientes -->

<div id="impClientes" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">
      <!-- Modal content-->
      <div class="modal-content">
          <div class="modal-header">
              <div class="col-md-12">
                <button type="button" class="close"><font id="cargando2"></font></button>
                <h5 class="modal-title"><b style="color:green;"><i class="icon-upload"></i> Importación de Clientes</b></h5>
            </div>
          </div>
          <form role="form" id="subida" enctype="multipart/form-data">
            <div class="modal-body">
              <h2 style="color:blue; text-align: center">¿Cómo subir un archivo .csv?</h2>  
              <div id="carouselSubirArchivo" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                  <li data-target="#carouselSubirArchivo" data-slide-to="0" class="active"></li>
                  <li data-target="#carouselSubirArchivo" data-slide-to="1"></li>
                </ol>
                <div class="carousel-inner">
                  <div class="item active">
                    <ol>
                      <li>En este apartado puedes importar tus clientes desde un archivo (.csv).</li>
                      <li>Primero tienes que convertir tu archivo a (.csv) para poder importar.</li>
                      <li>Luego das click en en botón seleccionar y eliges tu archivo.</li>
                      <li>La información debe estar ordenada y procura no dejar filas en blanco.</li>
                      <li>La información no debe contener cabeceras.</li>
                      <li>Los únicos datos a contemplar son en este orden: </li>
                        <ul>
                          <li>COLUMNA 1: Código (10 caracteres).</li>
                          <li>COLUMNA 2: Nombres.</li>
                          <li>COLUMNA 3: Apellido Paterno.</li>
                          <li>COLUMNA 4: Apellido Materno.</li>
                          <li>COLUMNA 5: Tipo.</li>
                            <ol>
                              <li>(Alumno Interno).</li>
                              <li>(Cliente Foráneo).</li>
                            </ol>
                          <li>COLUMNA 6: DNI. (8 caracteres).</li>
                        </ul>
                      <li>Recuerda que los códigos y los DNI deben ser únicos.</li>
                    </ol>
                  </div>
                  <div class="item">
                    <div class="form-group">
                      <div class="col-md-12">
                        <ol>
                          <li>Convierte tu arvhivo (.xls) a (.csv) separado por comas.</li>
                          <li>Guarda tu archivo (.csv) en el Escritorio.</li>
                          <li>Abre el archivo con el block de notas.</li>
                          <li>Selecciona en este formulario según el archivo. Puede ser por comas (,) o puntos y comas (;).</li>
                          <li>Sube tu archivo en el formulario debidamente ordenado. </li>
                          <li>Espera que se carguen los datos. </li>
                          <li>Listo!!</li>
                        </ol>
                        <hr>
                      </div>
                      <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-4">
                          <select class="form-control input-sm" name="separacion" id="separacion">
                            <option value=",">COMA (,)</option>
                            <option value=";">PUNTO Y COMA (;)</option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <input type="file" class="input-sm form-control" id="csv" name="csv">
                        </div>
                        <div class="col-md-5"></div>
                        <div class="col-md-7">
                          <span class="spn_form_personal" id="spn_csv"></span>
                        </div>
                      </div>
                      <br><br>
                    </div>
                  </div>
                </div>
                <a class="right carousel-control" href="#carouselSubirArchivo" data-slide="next">
                  <span class="glyphicon glyphicon-chevron-right"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
              <br>
            </div>
            <div class="modal-footer">
              <a class="btn btn-primary" id="btnCerrarImpCliente" data-dismiss="modal">Cerrar</a>
              <button id="btnImpCliente" type="submit" class="btn btn-success">Aceptar</button>
            </div>
          </form>
      </div>
    </div>
</div>
@endsection