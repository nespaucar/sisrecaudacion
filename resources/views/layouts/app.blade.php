<?php  
if(!Auth::guest()) {
    $type = Auth::user()->type; 
    if($type == 1) {
      $tipo = 'SUPER ADMINISTRADOR';
    } else if($type == 2) {
      $tipo = 'ADMINISTRADOR';
    } else if($type == 3) {
      $tipo = 'PERSONAL ORDINARIO';
    }
}
?>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de Recaudación FICSA - UNPRG</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href=" {{ asset('css/bootstrap2.min.css') }} " />

    <!-- Icono -->
    <link rel="icon" href="{{ asset('img/logo_ficsa.jpg') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.min.css') }}">

    <style>
        body {
            background: url('{{ asset('img/fondo.jpg') }}') no-repeat;
            background-attachment: fixed;
            background-size: cover;
            padding-top: 70px;
        }

        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }

        .spn_form_personal {
            color: red;
            font-size: 12px;
        }

        .spn_form_personal_usuario {
            color: blue;
            font-size: 12px;
        }
    </style>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-2.1.3.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <!-- Para Árboles -->
    <link rel="stylesheet" href="{{ asset('css/file-explore.css') }}"></link>
    <script src="{{ asset('js/file-explore.js') }}"></script>

    <!--búsqueda en vivo-->
    <link href=" {{ asset('css/select2.min.css') }} " rel="stylesheet" />
    <script src="{{ asset('js/select.min.js') }}"></script>

    <!--formato de fecha-->
    <link rel="stylesheet" href=" {{ asset('css/jquery-ui.css') }} ">
    <script src=" {{ asset('js/jquery-ui2.js') }} "></script>

    <script>
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '<Ant',
            nextText: 'Sig>',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd-mm-yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };

        $.datepicker.setDefaults($.datepicker.regional['es']);

        function dias_entre(date1, date2){ 
            if (date1.indexOf("-") != -1) { 
                date1 = date1.split("-"); 
            } else { 
                return 0; 
            } 

            if (date2.indexOf("-") != -1) { 
                date2 = date2.split("-"); 
            } else { 
                return 0; 
            } 

            if (parseInt(date1[2], 10) >= 1000) { 
               var sDate = new Date(date1[2]+"/"+date1[1]+"/"+date1[0]);
            } else { 
               return 0; 
            } 
            if (parseInt(date2[2], 10) >= 1000) { 
               var eDate = new Date(date2[2]+"/"+date2[1]+"/"+date2[0]);
            } else { 
               return 0; 
            } 
            var one_day = 1000*60*60*24; 
            var daysApart = Math.ceil((sDate.getTime()-eDate.getTime())/one_day); 
            return daysApart; 
        } 

        $(document).ready( function(){
            $('.spn_form_personal').addClass('hide');
        });

    </script>

</head>
<div id="numReciboModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="modal-title"><b style="color:green">Aperturar Caja: <b id="recibo"></b></b></h4>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <form role="form" id="formAddnum">
                        <div class="form-group">
                            <label for="nroant label-sm">No. Anterior</label>
                            <input type="text" class="form-control input-sm" id="nroant" readonly><br>
                        </div>
                        <div class="form-group">
                            <label for="nrohoy label-sm">No. de Hoy</label>
                            <div class="row">
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm" id="nrohoy">
                                </div>
                                <div class="col-md-3">
                                    <a id="add_numserie_sum_sheet_hoy" href="#" class="btn btn-primary btn-sm"><i class="icon-check"></i></a>
                                </div>
                                <div id='alertAddnum' class="col-md-12" style="color:red"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="footerAddnum" class="modal-footer hide">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    @if(isset($num_sum_sheet_hoy))
        @if($num_sum_sheet_hoy == '')
        <script>
            $(document).ready(function(){
                $("#numReciboModal").modal({
                    backdrop: 'static',
                    keyboard: true
                });
                $("#numReciboModal").modal("show");
                @if($num_sum_sheet_ant == '') 
                    $('#nroant').val('NO HAY HOJAS DE RESUMEN');
                @else
                    $('#nroant').val('<?php echo $num_sum_sheet_ant; ?>');
                @endif
                $('#nrohoy').val('<?php echo $num_sum_sheet_ant + 1 ?>');
            });
        </script>
        @endif
    @endif
<script type="text/javascript">
        $(document).on('click', '#add_numserie_sum_sheet_hoy', function(){
            var numserie = $('#nrohoy').val();
            if(!numserie) {
                $('#alertAddnum').html('Debes insertar un número.');
                $('#nrohoy').focus();
                return false;
            }

            $.ajax({
                url: 'add_numserie_sum_sheet_hoy/' + numserie,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'GET',
                dataType: 'json',
                success: function(result) {
                    if(result.estado == 'true') {
                        $('#formAddnum').html('<b style="color:blue">Se ha agregado correctamente el número de Caja del día de hoy:</b><br><center style="color:blue; font-size: 30px">' + numserie + '</center>');
                        $('#footerAddnum').removeClass('hide');
                    } else {
                        $('#alertAddnum').html('El número ingresado ya existe.');
                        $('#nrohoy').val('');
                        $('#nrohoy').focus();
                    }
                }
            }).fail(function(){ 
                alert('ALGO HA SALIDO MAL'); 
            });
        });
    </script>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="">
                        <div class="row">
                            <div class="col-lg-2">
                                <img src="{{ asset('img/iden.png') }}" class="img img-responsive center-block hidden-md hidden-sm hidden-xs" alt="" width="40%" height="40%">
                            </div>
                            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-10 pull-left">
                                Recaudación FICSA
                            </div>
                        </div>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">INGRESAR</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <i class="icon-user"></i> {{ $tipo }} | {{ Auth::user()->email }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <!-- Invocar Modal Cambio Contraseña -->
                                        <a id="abrirModalChangePass" data-target="#changePassModal" data-toggle="modal" href="#" onclick="">
                                            <i class="icon-edit"></i> CAMBIAR CONTRASEÑA
                                        </a>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="icon-off"></i> CERRAR SESIÓN
                                        </a>
                                        
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>
    
    <script>
        $(document).ready(function(){
            $("#alertasModal").modal("show");
            $("#txt_sequence").focus();
        });
    </script>
    
    <script>
        $(document).on('click','.eliminarBean', function(){
            var nombre = $('#nombre').val();
            var id = $('#id').val();
            if (nombre == undefined) { 
                nombre = $(this).data('nombre');
                id = $(this).data('id');
            } 
            var bean = $(this).data('bean');
            var table = $(this).data('table');
            $('#table').html(table);
            $('#mensajeBean').html('');
            $('#nombre_cc').html(nombre);

            var route1 = '{{ route('login') }}';

            var route = route1.substring(0, route1.length - 5) + 'eliminar1/' + id + '/' + bean;

            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'GET',
                dataType: 'json',
                success: function(result) {
                    $('#mensaje').html(result.mensaje);
                    $('#elimina2').data('id', id);
                    $('#elimina2').data('bean', bean);
                    $('#elimina2').show();
                    $('#mens_alerta').show();
                    $('#mens_elim').hide();
                }
            });     
        });
    </script>
    <script>
        $(document).on('click','#elimina2', function(){
            var id = $(this).data('id');
            var bean = $(this).data('bean');

            var route1 = '{{ route('login') }}';

            var route = route1.substring(0, route1.length - 5) + 'eliminar2/' + id + '/' + bean;

            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'GET',
                dataType: 'json',
                success: function(result) {
                    $('#mens_elim').html(result.mensaje);
                    $('#mens_elim').show();
                    $('#mens_alerta').hide();
                    $('#' + id).fadeOut("normal", function() {
                        $(this).remove();
                    });
                    $('.' + id).fadeOut("normal", function() {
                        $(this).remove();
                    });
                    $('#elimina2').hide();
                }
            });     
        });

        $(document).on('click', '#abrirModalChangePass', function() {
            $('#passAnt').focus();
            $('#passAnt').val('');
            $('#passNue').val('');
            $('#passNue2').val('');
            $('#changePass').removeClass('hide');
            $('.spn_form_personal').addClass('hide');
            $('#spn_passant').html('Ingresa Contraseña Anterior.');
            $('#spn_passant').html('Ingresa Contraseña Nueva.');
            $('#sms_changepass').addClass('hide').removeAttr('css').html('');
        });
    </script>

    <script>
        function validaInteger(numero) {
            if (!/^([0-9])*$/.test(numero)) {
                return false;
            } return true;
        }

        $(document).on('click', '#btn_sequence', function(){
            if($('#txt_sequence').val() == ''){
                $('#txt_sequence').focus();
                return false;
            }
            if(!validaInteger($('#txt_sequence').val())) {
                $('#txt_sequence').val('').focus();
                return false;
            }

            $.ajax({
                url: 'secuencia/' + $('#txt_sequence').val(),
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'GET',
                dataType: 'json',
                success: function(result) {
                    $('._sequence').html(result.sequence);
                }
            });
        });
    </script>

    
</body>
</html>
