<script>
    header('', '', 'COSTCENTERS', 'centro_de_costos', 5, '');

    $(document).ready(function() {
        $('.opcc').removeClass('closed').addClass('open');
    });

    $(document).on('click','.oplink', function(){
        var id = $(this).data('id');
        var codigo = $(this).data('codigo');
        var nombre = $(this).html();
        var padre = $(this).data('padre');

        $('.ppp').removeClass('hide');
        $('#' + id).addClass('hide');

        $(".oplink").css("background-color", "white");
        $("#nuevo_cc").data("id", $(this).data('id'));
        $(this).css("background-color", "yellow");
        $('#editar_cc').removeClass('hidden');

        if($(this).data('hijos') == 0) {
            $('#eliminarBean').removeClass('hidden');
        } else {
            $('#eliminarBean').addClass('hidden');
        }

        $('#nombre').attr('disabled', true);
        $('#padre').attr('disabled', true);

        $('#nombre').focus();

        $('#id').val(id);
        $('#codigo').val(codigo); 
        $('#nombre').val(nombre); 
        $('#padre').val(padre); 

        $('#mensajeBean').html('');
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
    });

    $(document).on('click','#nuevo_cc', function(){
        $('#nombre').removeAttr('disabled').focus();
        $('#padre').removeAttr('disabled');
        var cantidad = $('.opcc').length;
        $('#codigo').val('N° ' + cantidad);
        $('#id').val('SID');
        $('#nombre').val('');
        $('#padre').val($("#nuevo_cc").data('id'));
        $(".oplink").css("background-color", "white");

        $('#editar_cc').addClass('hidden');
        $('#eliminarBean').addClass('hidden');

        $('#conforme_cc').removeClass('edit').addClass('nue');

        $('#mensajeBean').html('');

        $('.ppp').removeClass('hide');
    });

    $(document).on('click','#editar_cc', function(){
        if(!$(this).attr('disabled')) {
            $('#nombre').removeAttr('disabled');
            $('#padre').removeAttr('disabled');
            $('#conforme_cc').removeClass('nue').addClass('edit');

            $('#mensajeBean').html('');
        }
    });    

    $(document).on('click','#conforme_cc', function(){
        if($('#nombre').val()) {
            var nombre = $('#nombre').val();
            var codigo = $('#codigo').val();
            var parent_id = $('#padre').val();

            var route = '';

            if($(this).attr('class') === 'btn btn-success btn-sm edit') {
                var id = $('#id').val();
                route = 'editarcentercost/' + id + '/' + codigo + '/' + nombre + '/' + parent_id;
            } else {
                route = 'nuevocentercost/' + codigo + '/' + nombre + '/' + parent_id;
            }

            $.ajax({
                url: route,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'GET',
                dataType: 'json',
                beforeSend: function(){
                    $("#mensajeBean").html("<img src='{{ asset('img/cargando.gif') }}' width='25' height='25' />");
                },
                success: function(result) {
                    var cadena = '';
                    var cadenap = '';
                    for (var i = 0; i < result.centercosts.length; i++) {
                        cadena += result.centercosts[i];
                    }

                    cadenap += '<select id="padre" name="padre" class="form-control input-sm form-control-success" disabled="">';
                    cadenap += '<option value="0">NUEVO</option>';
                    for (var i = 0; i < result.padres.length; i++) {
                        cadenap += result.padres[i];
                    }
                    cadenap += '</select>';
                    $('#centercosts').html(cadena);
                    $('#centercosts_select').html(cadenap);
                    $('.opcc').removeClass('closed').addClass('open');
                    $('#nombre').attr('disabled', true);
                    $('#nombre').val('');
                    $('#padre').attr('disabled', true);
                    $('#padre').val('0');
                    $('#id').val('SID');

                    $('#mensajeBean').html('<b style="color:green">Centro de Costos añadido o editado Correctamente.</b>');
                    $('#editar_cc').addClass('hidden');
                    $('#eliminarBean').addClass('hidden');
                }
            });

        } else {
            $('#mensajeBean').html('<b style="color:red">Debes digitar el nombre del Centro de Costos.</b>');
            $('#nombre').focus();
        }
    });

</script>