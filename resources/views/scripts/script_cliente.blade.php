<script>
    header('{{ route('buscarcliente') }}', '{{ route('nuevocliente') }}', 'CLIENTES', 'clientes', 4, '{{ csrf_field() }}');

	$(document).ready(function () {
		(function ($) {
			$('#search').keyup(function () {
				var rex = new RegExp($(this).val(), 'i');
				$('.recibo').hide();
				$('.recibo').filter(function () {
					return rex.test($(this).text());
				}).show();
			})
		}(jQuery));
	});

	$(document).on("click", ".detalles", function () {
		var id = $(this).data('id');
		var cliente = $(this).data('cliente');

		$('#cliente').html(cliente);

		var atras = '';

		if($(this).data('buscar')) {
			atras += '../../../';
		}

		$.ajax({
			url: atras + 'historialVentas/' + id,
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
							var deuda = '<b style="color:red;">Pendiente: ADEUDA</b>';
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
						texto += '<div class="col-md-3"><b style="color:blue;">Recibo: ' + result.recibos[i]['numrecibo'] + '</b></div>';
						texto += '<div class="col-md-3">' + estado + '</div>';
						texto += '<div class="col-md-3">' + deuda + '</div>';
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