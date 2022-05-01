<script>
	function hab(id){
		if($('#btn'+id).data('type') == 1) {
			$('#btn'+id).removeAttr('class').addClass('btn btn-danger').addClass('hab');
			$('#btn'+id).data('type', 0);
			$('#btn'+id).val('INHAB');
			$('#edtReg'+id).data('estado', '0');
		} else {
			$('#btn'+id).removeAttr('class').addClass('btn btn-success').addClass('hab');
			$('#btn'+id).data('type', 1);
			$('#btn'+id).val('HABIL');
			$('#edtReg'+id).data('estado', '1');

		}

		var id = $('#btn'+id).data('id');
		var cambio = $('#btn'+id).data('type')
    	var route = 'habUsuario/' + id + '/' + cambio;

		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET'
		}).fail(function(){
			alert('No se pudo Deshabilitar Usuario');
		});
	}

	$(document).ready(function (){
      	$('.solo-numero').keyup(function (){
        	this.value = (this.value + '').replace(/[^0-9]/g, '');
      	});
    });

	$(document).ready(function () {
		$('#usuario').val('');
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

	function cantcaracteres(cadena, lim) {
		if(cadena.length <= lim) {
			return cadena;
		} else {
			return cadena.substring(0, lim);
		}
	}

	window.onload = (function(){
	    $(".concatuser").on('keyup', function(){
	    	var dni = $('#dni').val();
	    	var nom = $('#nombres').val();
	    	var app = $('#apellidop').val();
	    	var apm = $('#apellidom').val();
	        var subn = cantcaracteres(nom, 3).toLowerCase();
	        var subap = cantcaracteres(app, 2).toLowerCase();
	        var subam = cantcaracteres(apm, 2).toLowerCase();
	        $("#usuario").val(dni + subn + subap + subam);
	    }).keyup();
	});

	$(document).ready(function(){
		$("#tipo").change(function(){
			var tipo = $(this).val();
			if(tipo == "1") {
            	$('#estado').val('1').attr('disabled', true);
            } else {
            	$('#estado').removeAttr('disabled');
            }
        });
	});

	$(document).on('click', ".nvoReg", function() {
		$('#spn_dni').html('DNI requerido, solo 8 caracteres.');
		$('#spn_email').html('Email Requerido, formato inválido.');

		$('#mensaje_personal').addClass('hide');
		$('#formulario_personal').removeClass('hide');

		$('.spn_form_personal').addClass('hide');
		$('#aceptarmodalpersonal').data('funcion', 'nuevo');
		$('#dni').val('').removeAttr('disabled');
		$('#telefono').val('');
		$('#nombres').val('');
		$('#apellidop').val('');
		$('#apellidom').val('');
		$('#direccion').val('');
		$('#email').val('');
		$('#tipo').val('3').removeAttr('disabled');
		$('#usuario').val('');
		$('#titulomodalpersonal').html('Nuevo Trabajador');
		$('#aceptarmodalpersonal').data('id', '-1');
		$('#aceptarmodalpersonal').removeClass('hide');
	});

	function cargar_formulario_editar(id, tipo) {
		$('#aceptarmodalpersonal').removeClass('hide');
		$('#spn_dni').html('DNI requerido, solo 8 caracteres.');
		$('#spn_email').html('Email Requerido, formato inválido.');

		$('#mensaje_personal').addClass('hide');
		$('#formulario_personal').removeClass('hide');

		$('.spn_form_personal').addClass('hide');
		$('#aceptarmodalpersonal').data('funcion', 'editar');

		var dni = $('#dni' + id).html().trim();
		var tel = $('#tel' + id).html().trim();
		var nom = $('#nom' + id).html().trim();
		var app = $('#app' + id).html().trim();
		var apm = $('#apm' + id).html().trim();
		var dir = $('#dir' + id).html().trim();
		var email = $('#email' + id).html().trim();

		var subn = cantcaracteres(nom, 3).toLowerCase();
        var subap = cantcaracteres(app, 2).toLowerCase();
        var subam = cantcaracteres(apm, 2).toLowerCase();

        var user1 = dni + subn + subap + subam;
        var user = (user1).replace(' ','');
        $("#usuario").val(user);

		$('#dni').val(dni).attr('disabled', true);
		$('#telefono').val(tel);
		$('#nombres').val(nom);
		$('#apellidop').val(app);
		$('#apellidom').val(apm);
		$('#direccion').val(dir);
		$('#email').val(email);

		$('#tipo').val(tipo);

		if(tipo == '1') {
			if(id == {{ Auth::user()->id }}) {
				$('#tipo').attr('disabled', true);
			} else {
				$('#tipo').removeAttr('disabled');
			}
		} else {
			$('#tipo').removeAttr('disabled');
		}
		
		$('#titulomodalpersonal').html('Editar Trabajador ' + $('#usuario').val());
		$('#aceptarmodalpersonal').data('id', id);
	}

	$(document).on('click', ".edtReg", function() {
		var id = $(this).data('id');
		var tipo = $(this).data('tipo');
		cargar_formulario_editar(id, tipo);
	});

	$(document).ready(function(){
		$('#aceptarmodalpersonal').click(function(){
			var id = $(this).data('id');
			var dni = $('#dni').val();
			var tel = $('#telefono').val();
			var nom = $('#nombres').val();
			var app = $('#apellidop').val();
			var apm = $('#apellidom').val();
			var dir = $('#direccion').val();
			var email = $('#email').val();
			var tipo = $('#tipo').val();
			var user = $('#usuario').val();
			$('#spn_dni').html('DNI requerido, solo 8 caracteres.');
			$('#spn_email').html('Email Requerido, formato inválido.');
			
			var caract = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
			var route = '';

			if(dni.length != 8) {
				$('.spn_form_personal').addClass('hide').slideDown('slow');
				$('#spn_dni').removeClass('hide').slideDown('fast');
				$('#dni').focus();
				return false;
			} if(tel.length != 9) {
				$('.spn_form_personal').addClass('hide').slideDown('slow');
				$('#spn_tel').removeClass('hide').slideDown('fast');
				$('#telefono').focus();
				return false;
			} if (nom.length > 50 || nom.length == 0) {
				$('.spn_form_personal').addClass('hide').slideDown('slow');
				$('#spn_nom').removeClass('hide').slideDown('fast');
				$('#nombres').focus();
				return false;
			} if (app.length > 50 || app.length == 0) {
				$('.spn_form_personal').addClass('hide').slideDown('slow');
				$('#spn_apm').removeClass('hide').slideDown('fast');
				$('#apellidop').focus();
				return false;
			} if(apm.length > 50 || apm.length == 0) {
				$('.spn_form_personal').addClass('hide').slideDown('slow');
				$('#spn_apm').removeClass('hide').slideDown('fast');
				$('#apellidom').focus();
				return false;
			}
			if (dir.length == 0 || dir.length == 0) {
				$('.spn_form_personal').addClass('hide').slideDown('slow');
				$('#spn_dir').removeClass('hide').slideDown('fast');
				$('#direccion').focus();
				return false;
			} if (email.length == 0 || caract.test(email) == false) {
				$('.spn_form_personal').addClass('hide').slideDown('slow');
				$('#spn_email').removeClass('hide').slideDown('fast');
				$('#email').focus();
				return false;
			} else {
				$('.spn_form_personal').addClass('hide').slideDown('slow');
			}

			if($(this).data('funcion') == 'nuevo') {
				route = 'nuevoPersonal/';
				route += dni + '/' + tel + '/' + nom + '/' + app + '/' + apm + '/' + dir + '/' + email + '/' + tipo + '/' + user;

				var dni_c = noduplicidad_(dni);
				var email_c = noduplicidad_(email);

				if(dni_c.existe == '1') {
					$('#spn_dni').removeClass('hide').html('DNI ya existe.');
					return false;
				} 

				if(email_c.existe == '1') {
					$('#spn_email').removeClass('hide').html('Email ya existe.');
					return false;
				}

				$.ajax({
					url: route,
					headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
					type: 'GET',
					dataType: 'JSON',
					beforeSend: function(){
						$('.spn_form_personal').addClass('hide').slideDown('slow');
	                    $("#cargando").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
	                },
	                success: function(res){
	                	$('#spn_dni').html('DNI requerido, solo 8 caracteres.');
						$('#spn_email').html('Email Requerido, formato inválido.');
						$("#cargando").html('');
						$('#mensaje_personal').removeClass('hide');
						$('#formulario_personal').addClass('hide');

						$('#mens_01').html(res.mensaje);
						$('.table tbody').prepend('<tr id="' + res.id + '"></tr>');
						$('#' + res.id).html(res.nuevoregistro);
						$('#aceptarmodalpersonal').addClass('hide');
	                }
				});

			} else {
				route = 'editarPersonal/';
				route += id + '/' + dni + '/' + tel + '/' + nom + '/' + app + '/' + apm + '/' + dir + '/' + email + '/' + tipo + '/' + user;

				var email_c = noduplicidad_(email);

				if(email_c.existe == '1' && email != $('#email' + id).html().trim()) {
					$('#spn_email').removeClass('hide').html('Email ya existe.');
					return false;
				}

				$.ajax({
					url: route,
					headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
					type: 'GET',
					dataType: 'JSON',
					beforeSend: function(){
						$('.spn_form_personal').addClass('hide').slideDown('slow');
	                    $("#cargando").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
	                },
	                success: function(res){
						$('#spn_email').html('Email Requerido, formato inválido.');
						$("#cargando").html('');
						$('#mensaje_personal').removeClass('hide');
						$('#formulario_personal').addClass('hide');
						$('#edtReg' + res.id).data('tipo', res.tipo);

						var tipor = res.tipo;

						if(tipor == '1') {
					        tipor = 'SUPER ADMINISTRADOR';
					        $('#btn' + res.id).removeAttr('class').attr('class', 'btn btn-primary').attr('value', 'HABIL').removeAttr('onclick').data('type', '1');

					    } else {
					    	if(tipor == '2') {
						        tipor = 'ADMINISTRADOR';
						    } else {
						        tipor = 'PERSONAL ORDINARIO';
						    }

						    $('#btn' + res.id).removeAttr('class').attr('class', 'btn btn-success').attr('value', 'HABIL').attr('onclick', "hab('" + res.id + "')").data('type', '1').data('id', res.id);
					    }

					    $('#mens_01').html(res.mensaje);
						$('#dni' + res.id).html(res.dni);
						$('#tel' + res.id).html(res.telefono);
						$('#nom' + res.id).html(res.nombres);
						$('#app' + res.id).html(res.apellidop);
						$('#apm' + res.id).html(res.apellidom);
						$('#dir' + res.id).html(res.direccion);
						$('#email' + res.id).html(res.email);
						$('#tipo' + res.id).html(tipor);

						$('#aceptarmodalpersonal').addClass('hide');
	                }
				});
			}
		});
	});

	function noduplicidad_(string){
		return JSON.parse($.ajax({
			url: 'noduplicidad/' + string,
			type: 'GET',
			async: false,
    		dataType: 'json',
            success: function(result) {
            	return result;
			}
		}).responseText);
	}
</script>