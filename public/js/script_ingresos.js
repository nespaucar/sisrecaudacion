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
	var cadena = $('#Concepto').val().split('@');
	var id = cadena[0];
	var p_actual = cadena[1];
	var igv = cadena[2];
	document.getElementById('p_actual').innerHTML = p_actual;
	document.getElementById('igv').innerHTML = igv;
	$('#p_real').val(parseFloat(p_actual) + parseFloat(igv));
}

function eliminardetalle(btn) {
	$($(btn).parent().parent()).fadeOut("normal", function() {
        $(this).remove();
    });
}

$(document).ready(function() {
    $('#Concepto').select2();

    cargadatos();

    $('#Concepto').change(function() {
    	cargadatos();
    });

    $('.nombre').val('Debes elegir un cliente');
	$('#escuela').html('-');
	$('#dni').html('-');

    $('#btnCliente').click(function(e) {
    	e.preventDefault();
    	if($('.codigo').val()) {
    		var codigo = $('.codigo').val();
    		var route = 'datoscliente/' + codigo;

    		$.ajax({
    			url: route,
    			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    			type: 'GET',
    			dataType: 'json',
    			data: {codigo: codigo},
    			success: function(result){
    				$('.codigo').val(result.codigo).addClass('alert-success').removeClass('alert-danger');
    				$('.nombre').val(result.nombres).addClass('alert-success').removeClass('alert-danger');
    				$('#escuela').html(result.escuela);
    				$('#dni').html(result.dni);
    				$('#mensajecliente').html('<b style="color:green;"><i class="icon-check"></i>  ¡Correcto!</b>');
    				$('#mensajeConcepto').html('');
    				$('#id_cliente_oculto').val(result.id);
    				$('#mensajeCliente').html('<b style="color:green">Cliente añadido Correctamente.</b>');
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
    });

    $('#btnConcepto').click(function(e) {
    	e.preventDefault();
    	var cantidad = $("#cantidad").val();
    	var p_real = $('#p_real').val();
    	if($('#id_cliente_oculto').val()) {
    		if(p_real && cantidad) {
    			if(validaFloat(p_real) && validaInteger(cantidad)) {
		            var tds=$("#tableDetalle thead tr th").length;
		            var nuevaFila="<tr>";
		            var cantidad = $("#cantidad").val();
		            var concepto = $("#Concepto option:selected").text();
		            var descripcion = $("#descripcion").val();
		            var p_real = $("#p_real").val();
		            for (var i = 0; i < tds; i++) {
		            	if(i==0){ nuevaFila+="<td>" + cantidad + "</td>"; }
		            	if(i==1){ nuevaFila+="<td>" + concepto + "</td>"; }
		            	if(i==2){ nuevaFila+="<td>" + descripcion + "</td>"; }
		            	if(i==3){ nuevaFila+="<td>" + p_real + "</td>"; }
		            	if(i==4){ nuevaFila+="<td>" + descripcion + "</td>"; }
		            	if(i==5){ nuevaFila+="<td>" + descripcion + "</td>"; }
		            	if(i==6){ nuevaFila+="<td><button type='button' class='elimdetalle btn btn-danger btn-sm' onclick='eliminardetalle(this)'><i class='icon-remove'></i></button></td>"; }
		            }
		            nuevaFila+="</tr>";
		            $("#tableDetalle").append(nuevaFila);
		            $('#mensajeConcepto').html('<b style="color:green">Concepto añadido correctamente.</b>');
		            $("#descripcion").val('');
		        } else {
		        	$('#mensajeConcepto').html('<b style="color:red">Ingresa campos válidos.</b>');
		        }
	        } else {
	        	$('#mensajeConcepto').html('<b style="color:red">No puedes dejar campos vacíos.</b>');
	        }
    	} else {
    		$('#mensajeConcepto').html('<b style="color:red">Tienes que elegir un Cliente primero.</b>');
    	}
    });

    
});