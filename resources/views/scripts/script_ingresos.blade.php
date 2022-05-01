<style>
    .seleccionado {
        background-color: yellow;
    }
</style>
<script>

    function validaFloat(numero) {
        if (!/^([0-9])*[.]?[0-9]*$/.test(numero)) {
            return false;
        } return true;
    }

    function validaInteger(numero) {
        if (!/^([0-9])*$/.test(numero)) {
            return false;
        } return true;
    }

    function cargadatos() {
        if($('#Tasa').val()){
            $('#cantidad').val('1');
            var cadena = $('#Tasa').val().split('@');
            var id = cadena[0];
            var p_actual = cadena[1];
            var igv = cadena[2];
            var descripcion = cadena[3];
            $('#p_actual').html(p_actual);
            $('#igv').html(igv);
            $('#p_real').val(parseFloat(p_actual) + parseFloat(igv));
            $('#importe').val(parseFloat(p_actual) + parseFloat(igv));   
            $('#descripcion').val(descripcion);
        }      
    }

    function cargartasas(idconcepto) {
        $.ajax({
            url: 'cargartasas/' + idconcepto,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            dataType: 'json',
            success: function(result){
                $('#Tasa option').remove();
                $('#Tasa').append(result.cadena);
            }
        })
    }

    function cargarconceptos(idconcepto) {
        $.ajax({
            url: 'cargarconceptos',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            dataType: 'json',
            success: function(result){
                $('#Concepto option').remove();
                $('#Concepto').append(result.cadena);
                $('#Concepto').val(idconcepto);
            }
        })
    }

    function eliminardetalle(btn) {
        $($(btn).parent().parent()).fadeOut("normal", function() {
            var num_fila = $(this).index() + 1;
            $(this).remove();
            $('#cant_detalles').val(parseInt($('#cant_detalles').val()) - 1);
            var cantidad =  $('#cant_detalles').val();
            var mod_filas = parseInt(cantidad) - parseInt(num_fila) + 1;
            //alert(mod_filas);
            var i = 1;
            if(parseInt(mod_filas) != 0){
                $("#tableDetalle").find(':input').each(function() {
                    var elemento = this;
                    if($(this).attr('class').indexOf('file') != -1) {
                        $(this).attr('id', 'inf_detalles' + i);
                        $(this).attr('name', 'inf_detalles' + i);
                        //alert('Id: ' + $(this).attr('id') + ' Value: ' + $(this).attr('value'));
                        i++;
                    }
                });
            }
            CalcularTotal();
        });
    }

    function CalcularTotal() {
        var toti = 0.00;
        var impi = 0.00;
        var saldi = 0.00;
        var $filas= $('#tableDetalle tbody tr');

        $filas.each(function() {
            $(this).find('td:eq(3)').each(function(i) {
                toti += parseFloat($(this).html());
            });
            $(this).find('td:eq(4)').each(function(i) {
                impi += parseFloat($(this).html());
            });
            $(this).find('td:eq(5)').each(function(i) {
                saldi += parseFloat($(this).html());
            });
        });
        $('.toti').html(toti.toFixed(2));
        $('.impi').html(impi.toFixed(2));
        $('.saldi').html(saldi.toFixed(2));
    }

    $(document).ready(function() {
        $(".oplink").click(function() {
            $(".oplink").css("background-color", "white");
            $(this).css("background-color", "yellow");
        });
    });

    $(document).ready(function() {
        $(".oplink").dblclick(function() {
            if($('#id_cliente_oculto').val()) {
                $("html, body").animate({scrollTop:"200%"});
                var id = $(this).data('id');
                cadena_costcenter(id);
                $('#centro_costos').val(id);
                $('#centercosts').addClass('hidden');
                $('#colapsar').removeClass('icon-collapse-top').addClass('icon-collapse');
                $(".colapsar").attr('id', 'true');
                $('#mensajeCentroCostos').html('<b style="color:green">Centro de Costos añadido Correctamente.</b>');
                $('#descripcion').focus();
            } else {
                $('#mensajeCentroCostos').html('<b style="color:red">Debes elegir primero un Cliente.</b>');
                $('#centercosts').addClass('hidden');
                $('#colapsar').removeClass('icon-collapse-top').addClass('icon-collapse');
                $('.colapsar').attr('id', 'true');
                $('#search').attr('disabled', 'disabled');
            }
        });
    });

    $(document).ready(function () {
        (function ($) {
            $('#search').keyup(function () {
                var rex = new RegExp($(this).val(), 'i');
                $('.opcc').hide();
                $('.opcc').filter(function () {
                    return rex.test($(this).text());
                }).show();
            })
        }(jQuery));

        $('#search').attr('disabled', 'disabled');
    });

    

    $(document).ready(function() {
        $(".colapsar").click(function() {
            var id = $(this).attr('id');
            if(id == 'true') {
                $('#centercosts').removeClass('hidden');
                $('#colapsar').removeClass('icon-collapse').addClass('icon-collapse-top');
                $(this).attr('id', 'false');
                $('#search').removeAttr('disabled', 'disabled');
                $('#search').focus();
            } else {
                $('#centercosts').addClass('hidden');
                $('#colapsar').removeClass('icon-collapse-top').addClass('icon-collapse');
                $(this).attr('id', 'true');
                $('#search').attr('disabled', 'disabled');
            }
        });
    });

    $(document).ready(function() {

        $("#btnVentasHoy").click(function() {
            var url = $(this).attr("href");
            window.open(url, '_blank');
            return false;
        });

        $('#Concepto').select2();
        $('#Tasa').select2();

        $('#Tasa').change(function() {
            if($(this).val() != '0' && $(this).val() != 'N'){
                cargadatos();
            } else {
                $('#cantidad').val('1');
                $('#p_actual').html('');
                $('#igv').html('');
                $('#p_real').val('');
                $('#importe').val('');  
                $('#descripcion').val('');  
            }
        });

        $('#Concepto').change(function() {
            if($(this).val() != '0'){
                cargartasas($(this).val());
            } else {
                $('#Tasa option').remove();
                $('#Tasa').append('<option value="0">-- SELECCIONA TASA --</option>');
            }
            $('#cantidad').val('0');
            $('#p_actual').html('');
            $('#igv').html('');
            $('#p_real').val('');
            $('#importe').val('');  
            $('#descripcion').val(''); 
            cargarconceptos($(this).val());
        });

        $('.nombre').val('Debes elegir un cliente');
        $('#escuela').html('-');
        $('#dni').html('-');

        $('#btnCliente').click(function(e) {
            e.preventDefault();
            @if($hoja_resumen == '')
                $('#mensajeCliente').html('<b style="color:red">Aún no se aperturó Caja.</b>');
                return false;
            @endif

            @if($estadocaja == 0) 
                if($('.codigo').val()) {
                    var codigo = $('.codigo').val();
                    var route = 'datoscliente/' + codigo;

                    $.ajax({
                        url: route,
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        type: 'GET',
                        dataType: 'json',
                        data: {codigo: codigo},
                        beforeSend: function(){
                            $("#mensajeCliente").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
                        },
                        success: function(result){
                            $('.codigo').val(result.codigo).addClass('alert-success').removeClass('alert-danger');
                            $('.nombre').val(result.nombres).addClass('alert-success').removeClass('alert-danger');
                            $('#escuela').html(result.escuela);
                            $('#dni').html(result.dni);
                            $('#mensajecliente').html('<b style="color:green;"><i class="icon-check"></i>  ¡Correcto!</b>');
                            $('#mensajeConcepto').html('');
                            $('#id_cliente_oculto').val(result.id);
                            $('#mensajeCliente').html('<b style="color:green">Cliente añadido Correctamente.</b>');
                            $('#centercosts').removeClass('hidden');
                            $('#colapsar').removeClass('icon-collapse').addClass('icon-collapse-top');
                            $('.colapsar').attr('id', 'false');
                            $('#search').removeAttr('disabled', 'disabled');
                            $('#search').focus();
                            if($('#escuela').html() == 'NO TIENE'){
                                $('#op_ba').addClass('hide');
                                $('#op_bt').removeClass('hide');
                                $('#op_ft').removeClass('hide');
                                $('#numrecibo').addClass('hide');
                                $('#apartado_tercero').removeClass('hide');
                                $('#comprobante').val('1');
                                $('#nrovoucher').val('');
                                $('#nrocomprobante').val('EB01-');
                            } else {
                                $('#op_ba').removeClass('hide');
                                $('#op_bt').addClass('hide');
                                $('#op_ft').addClass('hide');
                                $('#numrecibo').removeClass('hide');
                                $('#apartado_tercero').addClass('hide');
                                $('#comprobante').val('0');
                                $('#nrovoucher').val('');
                                $('#nrocomprobante').val('');
                            }
                        }
                    }).fail(function(){
                        $('.codigo').val('').addClass('alert-danger').removeClass('alert-success');
                        $('.nombre').val('').addClass('alert-danger').removeClass('alert-success');
                        $('#escuela').html('-');
                        $('#dni').html('-');
                        $('#mensajecliente').html('<b style="color:red;"><i class="icon-remove-circle"></i>  ¡Incorrecto!</b>');
                        $('#mensajeConcepto').html('');
                        $('#id_cliente_oculto').val('');
                        $('#mensajeCliente').html('<b style="color:red">No existe Cliente con el código ingresado.</b>');
                    });
                }  else {
                    $('#mensajeCliente').html('Debes escribir un código.');
                }
            @else
                $('#mensajeCliente').html('Ya cerraste Caja, espera hasta mañana.');
            @endif
        });

        $('#btnConcepto').click(function(e) {
            e.preventDefault();
            var cantidad = $("#cantidad").val();
            var p_real = $('#p_real').val();
            var importe = $('#importe').val();
            var descripcion = $('#descripcion').val();
            if($('#id_cliente_oculto').val() && $('#centro_costos').val()) {
                if(p_real && cantidad && importe) {
                    if(validaFloat(p_real) && validaFloat(importe) && validaInteger(cantidad)) {
                        if(parseFloat(importe) <= parseFloat(p_real)) {
                            var cadena = $('#Concepto').val().split('@');
                            var id_concepto = cadena[0];
                            var tds = $("#tableDetalle thead tr th").length;
                            var nuevaFila="<tr>";
                            var cantidad = $("#cantidad").val();
                            var concepto = $("#Concepto option:selected").text();
                            if($('#centro_costos').val() != '2' && $('#centro_costos').val() != '3' && $('#centro_costos').val() != '4'){
                                descripcion +=  ' ' + $(".centro_costos").val();
                            } 
                            var p_real = $("#p_real").val();
                            var importe = $("#importe").val();
                            $('#cant_detalles').val(parseInt($('#cant_detalles').val()) + 1);
                            var pos = $('#cant_detalles').val();

                            for (var i = 0; i < tds; i++) {
                                var a = parseFloat(p_real).toFixed(2);
                                var b = parseFloat(importe).toFixed(2);
                                var c = parseFloat(p_real - importe).toFixed(2);
                                if(i==0){ nuevaFila+="<td>" + cantidad + "<input type='hidden' class='file' name='inf_detalles" + pos + "' id='inf_detalles" + pos + "' value='" + id_concepto + "@@@" + descripcion + "@@@" + p_real + "@@@" + importe + "@@@" + cantidad + "' /></td>"; }
                                if(i==1){ nuevaFila+="<td>" + concepto + "</td>"; }
                                if(i==2){ nuevaFila+="<td>" + descripcion + "</td>"; }
                                if(i==3){ nuevaFila+="<td>" + a.toString() + "</td>"; }
                                if(i==4){ nuevaFila+="<td>" + b.toString() + "</td>"; }
                                if(i==5){ nuevaFila+="<td>" + c.toString() + "</td>"; }
                                if(i==6){ nuevaFila+="<td><button type='button' class='elimdetalle btn btn-danger btn-sm' onclick='eliminardetalle(this)'><i class='icon-remove'></i></button></td>"; }
                            }

                            nuevaFila+="</tr>";
                            $("#tableDetalle").append(nuevaFila);
                            $('#mensajeConcepto').html('<b style="color:green">Concepto añadido correctamente.</b>');
                            $("html, body").animate({scrollTop:"900%"});
                            //alert($('#cant_detalles').val() + ' ' + $("#inf_detalles" + pos).val());
                            
                            if($('#Tasa').val() == 'N') {
                                var descriocion = $('#descripcion').val();
                                var p_actual = $('#p_real').val();
                                var igv = '0';
                                var id_centro_costos = $('#Concepto').val();
                                var cantidad = $('#cantidad').val();
                                $.ajax({
                                    url: 'nuevaTasa/' + descripcion + '/' + p_actual + '/' + igv + '/' + id_centro_costos + '/' + cantidad,
                                    type: 'GET',
                                    success: function(){
                                        cargartasas($('#Concepto').val());
                                    }
                                }).fail(function(){
                                    alert('NO SE CREÓ NUEVA TASA');
                                });
                            }

                            // Llenar los inputs Ocultos
                            CalcularTotal();
                        } else {
                            $('#mensajeConcepto').html('<b style="color:red">El importe no puede ser mayor al monto.</b>');
                        }
                    } else {
                        $('#mensajeConcepto').html('<b style="color:red">Ingresa campos válidos.</b>');
                    }
                } else {
                    $('#mensajeConcepto').html('<b style="color:red">No puedes dejar campos vacíos.</b>');
                    $('#descripcion').focus();
                }
            } else {
                $('#mensajeConcepto').html('<b style="color:red">Tienes que elegir un Cliente y un Centro de Costos primero.</b>');
            }
        });

        $('#btnGuardarDetalles').click(function(e){
            e.preventDefault();
            GuardarDetalles(1);
        });

    });

    function GuardarDetalles(tipo) {
        var route = 'nuevoingreso';

        var idtasa = $('#Tasa').val().split('@')[0];

        if($('#comprobante').val() != '0'){
            if($('#nrovoucher').val() == ''){
                $('#nrovoucher').focus();
                return false;
            }
            if($('#nrocomprobante').val() == '' || $('#nrocomprobante').val().length == 5){
                $('#nrocomprobante').focus();
                return false;
            }
        }

        if($('#id_cliente_oculto').val() && $("#tableDetalle tbody tr").length != 0) {
            if($('#numrecibo').val()){
                $.ajax({
                    url: route,
                    type: 'GET',
                    dataType: 'json',
                    data: $("#formdetalles").serialize() + '&idtasa=' + idtasa + '&tipo=' + tipo,
                    beforeSend: function(){
                        $("#alerta_detalles").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
                        $("#cuerpoReimprModal").html("<center><img src='{{ asset('img/cargando.gif') }}' width='25' height='25' /></center>");
                    },
                    success: function(result){
                        $('#alerta_detalles').html('<b style="color:green">' + result.alerta + '.</b><br />').fadeIn().fadeOut(3000);
                        if($('#comprobante').val() != '0'){
                            noreimprimir('-');
                        } else {
                            $('#numrecibo').val(result.sequence);  
                            $("#reimprimirModal").modal({
                                backdrop: 'static',
                                keyboard: true
                            }); 
                            $('#reimprimirModal').modal("show");   
                            $('#cuerpoReimprModal').html('<h2 style="color:blue; text-align: center">Se Registró correctamente el Recibo Nº <b id="reimpNumRecibo"></b></h2>' + result.anul_ant);  
                            $('#reimpNumRecibo').html(parseInt(result.sequence) - 1);
                            $('#codig').focus();
                            $(".oplink").css("background-color", "white");
                            $('#Tasa option').remove();
                            $('#Tasa').append('<option value="0">-- SELECCIONA TASA --</option>');
                            $('#Tasa').val('0');
                            $('#cantidad').val('1');
                            $('#p_actual').html('');
                            $('#igv').html('');
                            $('#p_real').val('');
                            $('#importe').val('');  
                            $('#descripcion').val(''); 
                            cargartasas($('#Concepto').val());
                        }
                    }
                }).fail(function(){
                    $('#alerta_detalles').html('<b style="color:red">No se pudo registrar. Algo ha salido mal.</b><br />').fadeIn().fadeOut(3000);
                });
            } else {
                $('#numrecibo').focus();
                $('#alerta_detalles').html('<b style="color:red">Tienes que insertar un número de Recibo.</b><br />').fadeIn().fadeOut(3000);
            }
        } else {
            $('#alerta_detalles').html('<b style="color:red">No puedes registrar. No tienes un cliente o te faltan detalles.</b><br />').fadeIn().fadeOut(3000);
        }
    };

    $(document).ready(function() {
        $(".file-tree").filetree();
    });

    $(document).ready(function() {
        $(".file-tree").filetree({
            animationSpeed: 'fast'
        });
    });

    $(document).ready(function() {
        $(".file-tree").filetree({
            collapsed: true,
        });
    });

    function cadena_costcenter(id) {
        $.ajax({
            url: 'cadena_costcenter/' + id + '/ - ',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            dataType: 'json',
            success: function(result){
                $('#ccdescripcion').val(result.st1);
                $('.centro_costos').val(result.st2);
                if($('.centro_costos').val() == 'false'){
                    $('.centro_costos').val($('#ccdescripcion').val());
                }
            }
        }).fail(function(){
            alert('ACCION INCORRECTA');
        });
    }

    function noreimprimir(scroll = ''){
        $('.nombre').val('Debes elegir un cliente');
        $('.centro_costos').val('Debes Elegir un Centro de Costos');
        $('#ccdescripcion').val('Botón derecha y doble click para elegir un Centro de Costo');
        $('#escuela').html('-');
        $('#dni').html('-');
        $('.codigo').val('');
        $('#mensajeConcepto').html('');
        $('#mensajeCentroCostos').html('');
        $('#mensajeCliente').html('');
        $('#id_cliente_oculto').val('');
        $('#cant_detalles').val('0');
        //Restaurar la tabla
        $('#tableDetalle tbody tr').each(function() {
            $(this).fadeOut("normal", function() {
                $(this).remove();
            });
        });
        $('.toti').html('0.00');
        $('.impi').html('0.00');
        $('.saldi').html('0.00');
        $('#descripcion').val();
        $('#nom').removeClass('form-control-success').removeClass('alert-success');
        $('.nombre').removeClass('form-control-success').removeClass('alert-success');
        $('#codig').removeClass('alert-success');
        $('#nrovoucher').val('');
        $('#nrocomprobante').val('');
        if(scroll == ''){
            $("html, body").animate({scrollBottom:"500%"});
            $('#codig').focus();
        }
    };

    $(document).on('click', '#reimpNumReciboSig', function(e){
        e.preventDefault();
        GuardarDetalles(2);
    });

    /*$(document).on('click', '#reimpNumReciboSig', function(){
        var route = 'reimpNumReciboSig';
        $.ajax({
            url: route,
            type: 'GET',
            dataType: 'json',
            data: $("#formdetalles").serialize(),
            beforeSend: function(){
                $("#cuerpoReimprModal").html("<center><img src='{{ asset('img/cargando.gif') }}' width='25' height='25' /></center>");
            },
            success: function(result){
                $('#cuerpoReimprModal').html(result.alerta);  
                $('#numrecibo').val(result.numrecibo);  
            }
        }).fail(function(){
            alert('ALGO HA SALIDO MAL');
        });
    });*/
    $(document).on('focus', '#nrocomprobante', function(){
        if($('#comprobante').val() == '1'){
            $(this).val('EB01-');
        } else {
            $(this).val('E001-');
        }
    });
    $(document).on('change', '#comprobante', function(){
        if($(this).val() == '1'){
            $('#nrocomprobante').val('EB01-');
        } else {
            $('#nrocomprobante').val('E001-');
        }
    });
    $(document).on('keyup', '#nrocomprobante', function(){
        if($('#comprobante').val() == '1'){
            if($(this).val().length <= 5){
                $(this).val('EB01-');
            }
        } else {
            if($(this).val().length <= 5){
                $(this).val('E001-');
            }
        }
    });

    function cantidad(mod) {
        if ($('#p_real').val() && $('#importe').val()) {
            if (validaFloat($('#p_real').val()) && validaFloat($('#importe').val())) {
                if (parseInt($('#cantidad').val()) > 0) {
                    if($('#p_actual').html() == ''){
                        $('#p_actual').html($('#p_real').val());
                        $('#igv').html('0');
                    } 
                    var precio = parseFloat($('#p_actual').html()) + parseFloat($('#igv').html());
                    
                    if(mod == "+") {
                        $('#cantidad').val(parseInt($('#cantidad').val()) + 1);
                    } else {
                        if(parseInt($('#cantidad').val()) != 1) {
                            $('#cantidad').val(parseInt($('#cantidad').val()) - 1);
                        }
                    }
                    $('#p_real').val(precio * parseInt($('#cantidad').val()));
                    $('#importe').val(precio * parseInt($('#cantidad').val()));
                } 
            }
        }
    }
</script>