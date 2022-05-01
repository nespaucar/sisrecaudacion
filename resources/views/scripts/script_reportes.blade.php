<script>

	function daysInMonth(humanMonth, year) {
  		return new Date(year || new Date().getFullYear(), humanMonth, 0).getDate();
	}

    function hide() {
    	$('#_dia').addClass('hide');
    	$('#rango_dia').addClass('hide');
    	$('#_mes').addClass('hide');
    	$('#rango_mes').addClass('hide');
    	$('#_ano').addClass('hide');
    	$('#rango_ano').addClass('hide');

    	$('#mensajeReporte').html('');
    }

    function opcionreporte(id1, id2, id3) {
    	$("#" + id2).attr('style', 'background-color: #D5D5D5');
    	$("#" + id1).removeAttr('style');
    	$("#" + id3).removeAttr('style');
    	if(id2 == 'pccyf') {
    		$("#opcion_concepto_cc").removeClass('hide');
    		$("#opcion_fecha").addClass('hide');
    	} else {
    		$("#opcion_concepto_cc").addClass('hide');
    		$("#opcion_fecha").removeClass('hide');
    		if(id2 == 'pf'){
    			$('.btnR').removeAttr('form').attr('form', '_dia');
    			$('#tipo').val('D');
    			$('#intervalo').val('N');
    			$("#_dia").removeClass('hide');
    			$("#_mes").addClass('hide');
    			$("#_ano").addClass('hide');
    		}
    	}
    	$('div[id=ap_intervalo]').show();
    	$('div[id=ap_incluir]').show();
    }

    $(document).on('click', '#pccyf', function(){
    	$('select[id=tipo]').val('D');
    	hide();
    	if($("select[id=intervalo]").val() == 'N'){
			$('#_dia').removeClass('hide');
			$('.btnR').removeAttr('form').attr('form', '_dia');
		} else {
			$('#rango_dia').removeClass('hide');
			$('.btnR').removeAttr('form').attr('form', 'rango_dia');
		}
    });

    $(document).on('click', '#pempresa', function(){
    	$('select[id=tipo]').val('D');
    	$('select[id=intervalo]').val('N');
    	$('div[id=ap_intervalo]').hide();
    	$('div[id=ap_incluir]').hide();
    	hide();
    	if($("select[id=intervalo]").val() == 'N'){
			$('#_dia').removeClass('hide');
		} else {
			$('#rango_dia').removeClass('hide');
		}
		$('.btnR').removeAttr('form').attr('form', 'terceros');
    });

	$(document).ready(function() {

		hide();

		$('#_dia').removeClass('hide');
		
		$('select[id=intervalo]').change(function (){ 
			hide();
			if($(this).val() == 'N'){
				if($('select[id=tipo]').val() == 'D'){
					$('#_dia').removeClass('hide');
					$('.btnR').removeAttr('form').attr('form', '_dia');
				} else if($('select[id=tipo]').val() == 'M'){
					$('#_mes').removeClass('hide');
					$('.btnR').removeAttr('form').attr('form', '_mes');
				} else {
					$('#_ano').removeClass('hide');
					$('.btnR').removeAttr('form').attr('form', '_ano');
				}
			} else {
				if($('select[id=tipo]').val() == 'D'){
					$('#rango_dia').removeClass('hide');
					$('.btnR').removeAttr('form').attr('form', 'rango_dia');
				} else if($('select[id=tipo]').val() == 'M'){
					$('#rango_mes').removeClass('hide');
					$('.btnR').removeAttr('form').attr('form', 'rango_mes');
				} else {
					$('#rango_ano').removeClass('hide');
					$('.btnR').removeAttr('form').attr('form', 'rango_ano');
				}
			}
		})

		$('select[id=tipo]').change(function (){ 
			hide();
			if($(this).val() == 'D'){
				if($('select[id=intervalo]').val() == 'N'){
					$('#_dia').removeClass('hide');
					if($('.btnR').attr('form') != 'terceros') {
						$('.btnR').removeAttr('form').attr('form', '_dia');
					}
				} else {
					$('#rango_dia').removeClass('hide');
					if($('.btnR').attr('form') != 'terceros') {
						$('.btnR').removeAttr('form').attr('form', 'rango_dia');
					}
				}
			} else if($(this).val() == 'M'){
				if($('select[id=intervalo]').val() == 'N'){
					$('#_mes').removeClass('hide');
					if($('.btnR').attr('form') != 'terceros') {
						$('.btnR').removeAttr('form').attr('form', '_mes');
					}
				} else {
					$('#rango_mes').removeClass('hide');
					if($('.btnR').attr('form') != 'terceros') {
						$('.btnR').removeAttr('form').attr('form', 'rango_mes');
					}
				}
			} else {
				if($('select[id=intervalo]').val() == 'N'){
					$('#_ano').removeClass('hide');
					if($('.btnR').attr('form') != 'terceros') {
						$('.btnR').removeAttr('form').attr('form', '_ano');
					}
				} else {
					$('#rango_ano').removeClass('hide');
					if($('.btnR').attr('form') != 'terceros') {
						$('.btnR').removeAttr('form').attr('form', 'rango_ano');
					}
				}
			} 
		})
	})

	$(document).on('click', '.btnR', function(){
		var dia = $('#dia').val();
		var mes = $('#mes').val();
		var mano = $('#mano').val();
		var ano = $('#ano').val();

		var di = $('#di').val();
		var df = $('#df').val();
		var mi = $('#mi').val();
		var ami = $('#ami').val();
		var mf = $('#mf').val();
		var amf = $('#amf').val();
		var ai = $('#ai').val();
		var af = $('#af').val();

		var idconcepto = $('#op_concepto').val();
		var idcentrodecosto = $('#op_costcenter').val();

		var intervalo = $('#intervalo').val();
		var tipo = $('#tipo').val();
		var incluir = $('#incluir').val();
		var ordenar = $('#ordenar').val();

		var corr = 'Reporte Generado';
		var incorr = 'Datos Incorrectos';
		var incorrajax = 'Error al mostrar Datos'; 

		if($(this).val() == 'btnPDF') {
			var type = 2;
 		} else if ($(this).val() == 'btnEXC') {
			var type = 3;
		} else {
			var type = 1;
		}
		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////

		if($(this).attr('form') == '_dia'){
			if(dia) {
				var hoy = new Date();
				var fech = dia.split('-');
				var fech1 = new Date(fech[2], fech[1]-1, fech[0]);

				if(hoy < fech1) {
					$('#mensajeReporte').css('color', 'orange').html('Selecciona una fecha Anterior');
					return false;
				}

				if($('#opcion_concepto_cc').attr('class')) {
					var route = 'repDiario/' + type + '/' + dia + '/' + dia + '/' + ordenar + '/' + incluir;
				} else {
					var route = 'repConcCentroCosto/' + type + '/' + dia + '/' + dia + '/' + idconcepto + '/' + idcentrodecosto;
				}

				$.ajax({
	                url: route,
	                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
	                type: 'GET',
	                beforeSend: function(){
			            $("#mensajeReporte").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
			        },
	                success: function() {
	                	if(type == '1'){
	                		window.open(route, null, 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,titlebar=no');
	                	} else {
	                		location.href = route;
	                	}
	                   	
	                   $('#mensajeReporte').css('color', 'green').html(corr);
	                }
	            }).fail(function(){
        			$('#mensajeReporte').css('color', 'red').html(incorrajax);
        		});
			} else {
				$('#mensajeReporte').css('color', 'red').html(incorr);
			}

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////

		} else if($(this).attr('form') == '_mes') {
			var hoy = new Date();
			var fech = new Date(mano, mes, 01);

			if(hoy <= fech) {
				$('#mensajeReporte').css('color', 'orange').html('Selecciona un mes Anterior');
				return false;
			}
			
			var route = 'repMensual/' + type + '/' + mes + '-' + mano + '/' + mes + '-' + mano + '/' + ordenar + '/' + incluir;

			$.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'GET',
                beforeSend: function(){
		            $("#mensajeReporte").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
		        },
                success: function() {
                    if(type == '1'){
                		window.open(route, null, 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,titlebar=no');
                	} else {
                		location.href = route;
                	}

                	$('#mensajeReporte').css('color', 'green').html(corr);
                }
            }).fail(function(){
    			$('#mensajeReporte').css('color', 'red').html(incorrajax);
    		});

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////

		} else if($(this).attr('form') == '_ano') {
			var hoy = new Date();
			var anno = hoy.getFullYear();

			if(anno <= parseInt(ano)) {
				$('#mensajeReporte').css('color', 'orange').html('Selecciona un año Anterior');
				return false;
			}
			
			var route = 'repAnual/' + type + '/' + ano + '/' + ano + '/' + ordenar + '/' + incluir;

			$.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': '{ csrf_token() }}' },
                type: 'GET',
                beforeSend: function(){
		            $("#mensajeReporte").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
		        },
                success: function() {
                  if(type == '1'){
                		window.open(route, null, 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,titlebar=no');
                	} else {
                		location.href = route;
                	}
                   $('#mensajeReporte').css('color', 'green').html(corr);
                }
            }).fail(function(){
    			$('#mensajeReporte').css('color', 'red').html(incorrajax);
    		});

    	////////////////////////////////////////////////////////////////////////////////////////////////////////////

		} else if($(this).attr('form') == 'rango_dia') {
			if(dias_entre(df, di) > 0) {
				var hoy = new Date();
				var fi = di.split('-');
				var ff = df.split('-');
				var fech = new Date(fi[2], fi[1]-1, fi[0]);
				var fech1 = new Date(ff[2], ff[1]-1, ff[0]);

				if(hoy < fech1 || hoy < fech1) {
					$('#mensajeReporte').css('color', 'orange').html('Selecciona una fecha Anterior');
					return false;
				}

				if($('#opcion_concepto_cc').attr('class')) {
					var route = 'repDiario/' + type + '/' + di + '/' + df + '/' + ordenar + '/' + incluir;
				} else {
					var route = 'repConcCentroCosto/' + type + '/' + di + '/' + df + '/' + idconcepto + '/' + idcentrodecosto;
				}

				$.ajax({
	                url: route,
	                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
	                type: 'GET',
	                beforeSend: function(){
			            $("#mensajeReporte").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
			        },
	                success: function() {
	                	if(type == '1'){
	                		window.open(route, null, 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,titlebar=no');
	                	} else {
	                		location.href = route;
	                	}
	                   	
	                   $('#mensajeReporte').css('color', 'green').html(corr);
	                }
	            }).fail(function(){
        			$('#mensajeReporte').css('color', 'red').html(incorrajax);
        		});
			} else {
				$('#mensajeReporte').css('color', 'red').html(incorr);
			}

    	////////////////////////////////////////////////////////////////////////////////////////////////////////////

		} else if($(this).attr('form') == 'rango_mes') {
			var hoy = new Date();

			var fech1 = new Date(ami, mi, 01);
			var fech2 = new Date(amf, mf, 01);

			if(hoy <= fech1 || hoy <= fech2) {
				$('#mensajeReporte').css('color', 'orange').html('Selecciona un mes Anterior');
				return false;
			}

			if(parseInt(amf) - parseInt(ami) > 0) {
				var route = 'repMensual/' + type + '/' + mi + '-' + ami + '/' + mf + '-' + amf + '/' + ordenar + '/' + incluir;
				$.ajax({
	                url: route,
	                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
	                type: 'GET',
	                beforeSend: function(){
			            $("#mensajeReporte").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
			        },
	                success: function() {
	                	if(type == '1'){
	                		window.open(route, null, 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,titlebar=no');
	                	} else {
	                		location.href = route;
	                	}
	                   	
	                   $('#mensajeReporte').css('color', 'green').html(corr);
	                }
	            }).fail(function(){
	    			$('#mensajeReporte').css('color', 'red').html(incorrajax);
	    		});
			} else {
				if(parseInt(amf) - parseInt(ami) == 0 && parseInt(mf) - parseInt(mi) > 0) {
					var route = 'repMensual/' + type + '/' + mi + '-' + ami + '/' + mf + '-' + amf + '/' + ordenar + '/' + incluir;
					$.ajax({
		                url: route,
		                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
		                type: 'GET',
		                beforeSend: function(){
				            $("#mensajeReporte").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
				        },
		                success: function() {
		                	if(type == '1'){
		                		window.open(route, null, 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,titlebar=no');
		                	} else {
		                		location.href = route;
		                	}
		                   	
		                   $('#mensajeReporte').css('color', 'green').html(corr);
		                }
		            }).fail(function(){
		    			$('#mensajeReporte').css('color', 'red').html(incorrajax);
		    		});
				} else {
					$('#mensajeReporte').css('color', 'red').html(incorr);
				}
			}

    	////////////////////////////////////////////////////////////////////////////////////////////////////////////

		} else if($(this).attr('form') == 'rango_ano'){
			var hoy = new Date();
			var anno = hoy.getFullYear();

			if(anno <= parseInt(ai) || anno <= parseInt(af)) {
				$('#mensajeReporte').css('color', 'orange').html('Selecciona un año Anterior');
				return false;
			}
			if(parseInt(af) - parseInt(ai) > 0) {
				var route = 'repAnual/' + type + '/' + ai + '/' + af + '/' + ordenar + '/' + incluir;

				$.ajax({
	                url: route,
	                headers: {'X-CSRF-TOKEN': '{ csrf_token() }}' },
	                type: 'GET',
	                beforeSend: function(){
			            $("#mensajeReporte").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
			        },
	                success: function() {
	                  if(type == '1'){
                		window.open(route, null, 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,titlebar=no');
	                	} else {
	                		location.href = route;
	                	}
	                   $('#mensajeReporte').css('color', 'green').html(corr);
	                }
	            }).fail(function(){
	    			$('#mensajeReporte').css('color', 'red').html(incorrajax);
	    		});
			} else {
				$('#mensajeReporte').css('color', 'red').html(incorr);
			}
		} else {
			if(tipo == 'D'){
				fin = dia;
				ffi = dia;
				if(!dia){
					$('#mensajeReporte').css('color', 'red').html(incorr);
					return false;
				}
			} else if(tipo == 'M'){
				fin = '01-' + mes + '-' + mano;
				ffi = daysInMonth(mes, mano) + '-' + mes + '-' + mano;
			} else {
				fin = '01-01-' + mano;
				ffi = '31-12-' + mano;
			}
			route = 'repTerceros/' + type + '/' + fin + '/' + ffi + '/id/' + tipo;
			$.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': '{ csrf_token() }}' },
                type: 'GET',
                beforeSend: function(){
		            $("#mensajeReporte").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
		        },
                success: function() {
                  if(type == '1'){
            		window.open(route, null, 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,titlebar=no');
                	} else {
                		location.href = route;
                	}
                   $('#mensajeReporte').css('color', 'green').html(corr);
                }
            }).fail(function(){
    			$('#mensajeReporte').css('color', 'red').html(incorrajax);
    		});
		}
	})
</script>