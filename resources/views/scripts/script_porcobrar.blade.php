<script>
    header('{{ route('buscarcliente') }}', '{{ route('nuevocliente') }}', 'DEUDAS', 'porcobrar', 4, '{{ csrf_field() }}');

	function validaFloat(numero) {
        if (!/^([0-9])*[.]?[0-9]*$/.test(numero)) {
            return false;
        } return true;
    }

	$(document).ready(function () {
		(function ($) {
			$('#search').keyup(function () {
				var rex = new RegExp($(this).val(), 'i');
				$('#mantenimiento tbody tr').hide();
				$('#mantenimiento tbody tr').filter(function () {
					return rex.test($(this).text());
				}).show();
			})
		}(jQuery));
	});

	$(document).on("click", ".mod_deuda", function () {
		var numrecibo = $(this).data('recibo');
		var id = $(this).data('id');
		var deuda = $(this).data('deuda');
		$("#detallesModal").modal("hide");
		$("#abonarModal").modal("show");
		$('#rec_deuda').html(numrecibo);
		$('#m_Total').html(deuda);
		$('#id_table').val(id);
		$('#m_Abono').val('');
		$('#FormDeuda').show();
		$('#alertaFormDeuda').hide();
	})

	$(document).on("click", "#btnAbonar", function () {
		var m_total = parseFloat($('#m_Total').html());
		var m_abono = parseFloat($('#m_Abono').val());
		var dif = m_total - m_abono;
		var recibo = $('#rec_deuda').html();
		var id = $('#id_table').val();

		$('#al_sms_Abono').removeClass('hide');

		if(validaFloat(m_abono)){
			
			if(dif >= 0){
				$('#al_sms_Abono').addClass('hide');

            	var route = 'abonarDeuda/' + recibo + '/' + m_abono;

				$.ajax({
	                url: route,
	                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
	                type: 'GET',
	                dataType: 'json',
	                beforeSend: function(){
			            $("#sms_Abono").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
			        },
	                success: function(result) {
	                	var deuda_ant = $('#tblDeudas #' + id).find("td")[6].innerHTML;
	                	var deuda_total_actual = (parseFloat(deuda_ant) - parseFloat(result.abono)).toFixed(2);
						$('#tblDeudas #' + id).find("td")[6].innerHTML = deuda_total_actual;
						$('#FormDeuda').hide();
						$('#alertaFormDeuda').show();
	                    $('#alertaFormDeuda').html('<div class="row"><div class="col-md-12"><b style="color:green">SE HA ABONADO S/.' + parseFloat(result.abono).toFixed(2) + ' A ESTE RECIBO.<hr> QUEDAN S/. ' + parseFloat(result.deuda).toFixed(2) + ' PEDIENTES PARA ESTE RECIBO. <hr> EN TOTAL QUEDAN S/. ' + deuda_total_actual + ' PENDIENTES PARA ESTE CLIENTE.</b></div></div>');
	                    if(parseFloat(result.deuda).toFixed(2) == '0.00') {
	                    	var cant_ant = $('#tblDeudas #' + id).find("td")[5].innerHTML;
	                    	$('#tblDeudas #' + id).find("td")[5].innerHTML = (parseInt(cant_ant) - 1).toString();
	                    	if($('#tblDeudas #' + id).find("td")[5].innerHTML == '0') {
	                    		$('#tblDeudas #' + id).remove();
	                    	}

	                    }
						
	                }
	            });
			} else {
				$('#sms_Abono').html('INGRESA VALOR VÁLIDO.');
			}
		} else {
			$('#sms_Abono').html('INGRESA UN FORMATO VÁLIDO.');
		}
	})

	$(document).on("click", ".detalles", function () {
		var id = $(this).data('id');
		var cliente = $(this).data('cliente');

		$('#cliente').html(cliente);

		var route = 'historialDeudas/' + id;
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
			dataType: 'json',
			beforeSend: function(){
	            $("#Historial").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
	        },
			success: function(result){
				$('#search').val('');
				$('#Historial').html('');
				$('#Alerta').html('');
				var texto = '';
				if(result.recibos.length != 0){
					for (var i = 0; i < result.recibos.length; i++) {
						var impi = 0.00;
						var saldi = 0.00;

						if(result.recibos[i]['estado'] == 0){
							var deuda = '<b style="color:red;">Pendiente: Debe s/. ' + parseFloat(result.deudas[i]['deuda']).toFixed(2) + '</b>';
						} else {
							var deuda = '<b style="color:blue;">Pendiente: CANCELADO</b>';
						}
						if(result.recibos[i]['anulado'] == 0){
							var estado = '<b style="color:red;">Estado: ANULADO</b>';
						} else {
							var estado = '<b style="color:blue;">Estado: REGISTRADO</b>';
						}
						texto += '<div class="recibo"><div class="row">';
						texto += '<div class="col-md-3"><b style="color:blue;"> Fecha: ' + result.recibos[i]['fecha'] + '</b></div>'
						texto += '<div class="col-md-2"><b style="color:blue;">Recibo: ' + result.recibos[i]['numrecibo'] + '</b></div>';
						texto += '<div class="col-md-3">' + estado + '</div>';
						texto += '<div class="col-md-3">' + deuda + '</div>';
						texto += '<div class="col-md-1"><button class="mod_deuda btn btn-sm btn-danger" data-recibo="' + result.recibos[i]['numrecibo'] + '" data-deuda="' + parseFloat(result.deudas[i]['deuda']).toFixed(2) + '" data-id="' + id + '"><i class="icon-edit"></i></button></div>';
						texto += '</div><hr />';

						texto += '<table class="table table-responsive">';
						texto += '<thead><tr>';
						texto += '<th>CANTIDAD</th>';
						texto += '<th>CONCEPTO</th>';
						texto += '<th>DESCRIPCIÓN</th>';
						texto += '<th>P.TOTAL</th>';
						texto += '<th>IMPORTE</th>';
						texto += '<th>SALDO</th>';
						texto += '</tr></thead>';

						texto += '<tbody>';

						for (var a = 0; a < result.detalles[i].length; a++) {
							texto += '<tr>' +
								'<td>' + result.detalles[i][a]['cantidad'] + '</td>' +
								'<td>' + result.detalles[i][a]['descripcion'] + '</td>' +
								'<td>' + result.detalles[i][a]['dp'] + '</td>' +
								'<td>' + parseFloat(result.detalles[i][a]['p_real']).toFixed(2) + '</td>' +
								'<td>' + parseFloat(result.detalles[i][a]['importe']).toFixed(2) + '</td>' +
								'<td>' + parseFloat(result.detalles[i][a]['p_real'] - result.detalles[i][a]['importe']).toFixed(2) + '</td>' +
							'</tr>';
							impi += parseFloat(result.detalles[i][a]['importe']);
							saldi += parseFloat(result.detalles[i][a]['p_real'] - result.detalles[i][a]['importe']);
						}

						texto += '<tfoot><tr>';
						texto += '<th></th>';
						texto += '<th></th>';
						texto += '<th></th>';
						texto += '<th>TOTAL</th>';
						texto += '<th>' + impi.toFixed(2) + '</th>';
						texto += '<th>' + saldi.toFixed(2) + '</th>';
						texto += '</tr></tfoot>';

						texto += '</tbody>';

						texto += '</table><hr /></div>';
						//impi += parseFloat(result.recibos[i]['importe']);
						//saldi += parseFloat(result.recibos[i]['p_real']*result.recibos[i]['cantidad'] - result.recibos[i]['importe']);
					}
					$('#Historial').html(texto);
				} else {
					$('#Alerta').html('<b style="color: red;">Este Cliente no tiene Ventas Registradas</b>');
				}
			}
		}).fail(function(){
			$('#tableDetalles').html('<tr><td colspan="6"> Ha ocurrido un error</td></tr>');
		});
	});
</script>