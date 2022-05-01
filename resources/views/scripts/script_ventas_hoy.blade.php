<script>
	@if($caja[0]['estado'] == 1)
		$(document).ready(function () {
			$('.anul').addClass('hide');
		});
	@endif
	$(document).ready(function () {
		(function ($) {
			$('#search').keyup(function () {
				var rex = new RegExp($(this).val(), 'i');
				$('tbody tr').hide();
				$('tbody tr').filter(function () {
					return rex.test($(this).text());
				}).show();
			})
		}(jQuery));
	});

	$(document).on("click", ".btnCaj_ant", function () { 
		var caja_ant = $(this).data('deudadia');
		$('#formPapeletas').collapse('hide');
		var op = $(this).data('op');
		var fecha = $(this).data('fecha');

		$("#fe_ant").val(fecha);

		if(op == '1') {
			$('#formPapeletas_ant').collapse('hide');
			$('.btnCaj_ant').data('op', '1');
			$(this).data('op', '0');
		} else {
			$('#formPapeletas_ant').collapse('show');
			$('.btnCaj_ant').data('op', '0');
			$(this).data('op', '1');
			$('#mtotal_ant').html(caja_ant);
		}

		$('.btnCaj_ant').removeClass('btn-info').addClass('btn-warning');
		$(this).removeClass('btn-warning').addClass('btn-info');
		$('#sms_ant').html('');
		$('#caja0').removeClass('hide');
		$('#caja00').html('');

		$('#np1_ant').val('');
		$('#np2_ant').val('');
		$('#np3_ant').val('');
		$('#np4_ant').val('');
		$('#mp1_ant').val('');
		$('#mp2_ant').val('');
		$('#mp3_ant').val('');
		$('#mp4_ant').val('');
	});

	$(document).on("click", ".btnRes", function () { 
		$('.btnCaj_ant').removeClass('btn-info').addClass('btn-warning');
		$('#formPapeletas_ant').collapse('hide');
		$('#sms').html('');
	});

	$(document).on("click", ".btnMenAlert", function () { 
		$('#caja00').hide();
	});

	$(document).on("click", ".detalles", function () {

		var recibo = $(this).data('recibo');
		var cliente = $(this).data('cliente');
		var id = $(this).data('id');
		$('#recibo').html(recibo);
		$('#cliente').html(cliente);

		var route = 'detalles/' + id;
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
			dataType: 'json',
			beforeSend: function(){
	            $("#tablaDetalles").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
	        },
			success: function(result){
				var texto = '';
				var impi = 0.00;
				var saldi = 0.00;
				for (var i = 0; i < result.alerta.length; i++) {
					texto += '<tr>' +
						'<td>' + result.alerta[i]['cantidad'] + '</td>' +
						'<td>' + result.alerta[i]['condescripcion'] + '</td>' +
						'<td>' + result.alerta[i]['descripcion'] + '</td>' +
						'<td>' + parseFloat(result.alerta[i]['p_real']).toFixed(2) + '</td>' +
						'<td>' + parseFloat(result.alerta[i]['importe']).toFixed(2) + '</td>' +
						'<td>' + parseFloat(result.alerta[i]['p_real'] - result.alerta[i]['importe']).toFixed(2) + '</td>' +
					'</tr>';
					impi += parseFloat(result.alerta[i]['importe']);
					saldi += parseFloat(result.alerta[i]['p_real'] - result.alerta[i]['importe']);
				}

				$('#tablaDetalles').html(texto);
				$('.impi').html(impi.toFixed(2));
				$('.saldi').html(saldi.toFixed(2));
			}
		}).fail(function(){
			$('#tableDetalles').html('<tr><td colspan="6"> Ha ocurrido un error</td></tr>');
		});
	});

	$(document).on("click", ".btnAnularRecibo", function (e) {
		e.preventDefault();
		var id = $(this).data('id');
		var cambio = $(this).data('cambio');
    	var route = 'anularrecibo/' + id + '/' + cambio;
    	if(cambio == '1'){
    		var reemplazo = '<a data-id="'+ id +'" data-cambio="0" class="btnAnularRecibo btn btn-danger btn-sm">Anular</i></a>';
    		var reemplazo2 = '<font style="color: green; font-weight: bold;">GUARDADO</font>';
    	} else {
    		var reemplazo = '<a data-id="'+ id +'" data-cambio="1" class="btnAnularRecibo btn btn-primary btn-sm">Recup.</i></a>';
    		var reemplazo2 = '<font style="color: red; font-weight: bold;">ANULADO</font>';
    	}

    	$(this).parent().html(reemplazo);

    	$('.' + id).html(reemplazo2);

    	$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
			dataType: 'json',
			success: function(result) {
				$('#mtotal').html(parseInt(result.total));
				if(parseInt(result.total) != 0) {
					$('#ParaCerrarCaja').html('<div class="col-md-2"><button class="btnRes btn btn-sm btn-success" data-toggle="collapse" data-target="#formPapeletas">CERRAR</button></div>');
				} else {
					$('#ParaCerrarCaja').html('<div class="col-md-4"><font size="2px">Si no hay ventas hoy, este apartado se eliminará automáticamente el día de mañana.</font></div>');
				}
			}
		}).fail(function(){
			alert('No se pudo Anular Recibo');
		});
	});

	$(document).on("click", "#regpapeletas", function (e) {
		e.preventDefault();

		var papeleta = true;
		if($('#mp1').val()) {
			var mp1 = $('#mp1').val();
			if($('#np1').val()) {
				var np1 = $('#np1').val();
				papeleta = true;
			} else {
				papeleta = false;
			}
		} else {
			var mp1 = 0.00;
			if($('#np1').val()) {
				papeleta = false;
			}
		}
		if($('#mp2').val()) {
			var mp2 = $('#mp2').val();
			if($('#np2').val()) {
				var np2 = $('#np2').val();
				papeleta = true;
			} else {
				papeleta = false;
			}
		} else {
			var mp2 = 0.00;
			if($('#np2').val()) {
				papeleta = false;
			}
		}
		if($('#mp3').val()) {
			var mp3 = $('#mp3').val();
			if($('#np3').val()) {
				var np3 = $('#np3').val();
				papeleta = true;
			} else {
				papeleta = false;
			}
		} else {
			var mp3 = 0.00;
			if($('#np3').val()) {
				papeleta = false;
			}
		}
		if($('#mp4').val()) {
			var mp4	= $('#mp4').val();
			if($('#np4').val()) {
				var np4 = $('#np4').val();
				papeleta = true;
			} else {
				papeleta = false;
			}
		} else {
			var mp4 = 0.00;
			if($('#np4').val()) {
				papeleta = false;
			}
		}

		var mtotal = parseFloat($('#mtotal').html());

		var ptotal = parseFloat(mp1) + parseFloat(mp2) + parseFloat(mp3) + parseFloat(mp4);

		if(mtotal != ptotal) {
			$('#sms').html('LOS MONTOS NO COINCIDEN, REVISA...');
		} else {
			if(papeleta == false) {
				$('#sms').html('DATOS INCORRECTOS, REVISA...');
			} else {
		    	var route = 'regpapeletas/' + np1 + '/' + np2 + '/' + np3 + '/' + np4 + '/' + mp1 + '/' + mp2 + '/' + mp3 + '/' + mp4;

		    	$.ajax({
					url: route,
					headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
					type: 'GET',
					dataType: 'json',
					beforeSend: function(){
			            $("#sms").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
			        },
					success: function(result){
						$('#caja01').addClass('hide');
						$('#caja02').addClass('hide');
						$('#caja03').html(result.alerta);
						$('#caja03').removeClass('hide');
						$('#malertas').html(parseFloat($('#malertas').html()) - 1);
						$('.anul').addClass('hide');
					}
				}).fail(function(){
					alert('No se pudo Registrar Resumen');
				});
			}
		}
	});

	$(document).on("click", "#regpapeletas_ant", function (e) {
		e.preventDefault();

		var fecha = $("#fe_ant").val();

		var papeleta = true;
		if($('#mp1_ant').val()) {
			var mp1 = $('#mp1_ant').val();
			if($('#np1_ant').val()) {
				var np1 = $('#np1_ant').val();
				papeleta = true;
			} else {
				papeleta = false;
			}
		} else {
			var mp1 = 0.00;
			if($('#np1_ant').val()) {
				papeleta = false;
			}
		}
		if($('#mp2_ant').val()) {
			var mp2 = $('#mp2_ant').val();
			if($('#np2_ant').val()) {
				var np2 = $('#np2_ant').val();
				papeleta = true;
			} else {
				papeleta = false;
			}
		} else {
			var mp2 = 0.00;
			if($('#np2_ant').val()) {
				papeleta = false;
			}
		}
		if($('#mp3_ant').val()) {
			var mp3 = $('#mp3_ant').val();
			if($('#np3_ant').val()) {
				var np3 = $('#np3_ant').val();
				papeleta = true;
			} else {
				papeleta = false;
			}
		} else {
			var mp3 = 0.00;
			if($('#np3_ant').val()) {
				papeleta = false;
			}
		}
		if($('#mp4_ant').val()) {
			var mp4	= $('#mp4_ant').val();
			if($('#np4_ant').val()) {
				var np4 = $('#np4_ant').val();
				papeleta = true;
			} else {
				papeleta = false;
			}
		} else {
			var mp4 = 0.00;
			if($('#np4_ant').val()) {
				papeleta = false;
			}
		}

		var mtotal = parseFloat($('#mtotal_ant').html());

		var ptotal = parseFloat(mp1) + parseFloat(mp2) + parseFloat(mp3) + parseFloat(mp4);

		if(mtotal != ptotal) {
			$('#sms_ant').html('LOS MONTOS NO COINCIDEN, REVISA...');
		} else {
			if(papeleta == false) {
				$('#sms_ant').html('DATOS INCORRECTOS, REVISA...');
			} else {
		    	var route = 'regpapeletas_ant/' + fecha + '/' + np1 + '/' + np2 + '/' + np3 + '/' + np4 + '/' + mp1 + '/' + mp2 + '/' + mp3 + '/' + mp4;

		    	$.ajax({
					url: route,
					headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
					type: 'GET',
					dataType: 'json',
					beforeSend: function(){
			            $("#sms_ant").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
			        },
					success: function(result){
						$('#caja0').addClass('hide');
						$('#caja00').show();
						$('#caja00').html(result.alerta);
						$('#caja00').removeClass('hide');
						$('#c_abiertas').html(parseFloat($('#c_abiertas').html()) - 1);
						if($('#c_abiertas').html() == '0') {
							$('#malertas').html(parseFloat($('#malertas').html()) - 1);
						}
						$('#' + fecha).hide();
					}
				}).fail(function(){
					alert('No se pudo Registrar Resumen');
				});
			}
		}
	});

</script>

