function setRun(url, par, div){
    div = "#"+div;
    url = url+".php";
    if(par != ''){
        url = url + "?" + par;
    }
    $(div).html(imgCargando());
    $.post(url, {}, function(data){
        $(div).html(data);
    });
}

function imgCargando () {
    var texto = "<b class='text-center'>Cargando...</b>";
    return texto;
}

function nuevo(model) {
    $(document).ready(function() {
        $('#mantenimiento').load(model);
    });
}

function header(routeform, routenuevo, titulo, active, col, csrf) {
    var texto = 
    '<div class="row">' +
        '<div class="col-md-' + col + '"><h4>MANTENIMIENTO DE ' + titulo + '</h4></div>' +
        '<div class="col-md-' + (11 - col) + '">' +
            '<div class="row">';
                var buscador = '<form role="search" action="' + routeform + '">' +
                    csrf + 
                    '<div class="input-group">' + 
                        '<input type="text" class="form-control" name="search" maxlength="30">';

                        var texto2 = categorias(titulo);

                        var texto3 = '<span class="input-group-btn">' +
                            '<button type="submit" class="btn btn-default">Buscar</button>' +
                        '</span>' +
                    '</div>' +
                '</form>' + 
            '</div>' +
        '</div>' +
        '<div class="col-md-1"><a href="' + routenuevo + '" class="btn btn-success btn-sm pull-right"><i class="icon-plus-sign-alt"></i></a>' +
        '</div>' +
    '</div>'
    ;

    if(titulo == 'INGRESOS' || titulo == 'REPORTES') {
        texto3 = '';
        buscador = '';
    }

    if(titulo == 'DEUDAS' || titulo == 'COSTCENTERS' || titulo == 'PERSONAL') {
        buscador = '';
        texto3 = '<div class="col-md-12">' + 
                    '<div class="input-group">' +
                        '<input type="text" id="search" class="form-control">' +
                        '<span class="input-group-addon"><i class="icon-search"></i></span>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    if(titulo == 'PERSONAL') {
        texto3 = texto3 + '<div class="col-md-1"><a data-toggle="modal" data-target="#modalpersonal" href="' + routenuevo + '" class="btn btn-success btn-sm pull-right nvoReg"><i class="icon-plus-sign-alt"></i></a>' +
        '</div>';
    }

    if(titulo == 'CLIENTES') {
        texto3 = texto3 + '<div class="text-right"><button class="btn btn-primary btn-sm btnImpClientes" data-toggle="modal" data-target="#impClientes"><i class="icon-upload"></i> IMPORTAR</button>';
    }

    $('#titulo').html(texto + buscador + texto2 + texto3);
    $('#'+ active).addClass('active');
}

function categorias(titulo) {

    switch(titulo) {
        case 'CLIENTES':
        return  '<span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>' +
            '<select name="tipo" class="form-control">' +
                '<option value="0">Todo tipo</option>' +
                '<option value="1">Al. Interno</option>' +
                '<option value="2">Al. Foráneo</option>' +
            '</select>' +
            '<span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>' +
            '<select name="campo" class="form-control">' +
                '<option value="0">Todo campo</option>' +
                '<option value="nombres">Nombres</option>' +
                '<option value="codigo">Código</option>' +
                '<option value="dni">DNI</option>' +
            '</select>';
        break;

        case 'FACULTADES':
        return '<span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>' +
            '<select name="campo" class="form-control">' +
                '<option value="0">Todo campo</option>' +
                '<option value="codigo">Código</option>' +
                '<option value="nombre">Nombre</option>' +
            '</select>';
        break;

        case 'ESCUELAS':
        return '<span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>' +
            '<select name="campo" class="form-control">' +
                '<option value="0">Todo campo</option>' +
                '<option value="codigo">Código</option>' +
                '<option value="nombre">Nombre</option>' +
            '</select>';
        break;
        
        case 'INGRESOS':
        return '';
        break;

        case 'CONCEPTOS':
        return '<span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>' +
            '<select name="campo" class="form-control">' +
                '<option value="0">Todo campo</option>' +
                '<option value="financialclassifiers.codigo">Clas. Finan.</option>' +
                '<option value="budgetclassifiers.codigo">Clas. Presup.</option>' +
                '<option value="descripcion">Descripción</option>' +
            '</select>';
        break;

        case 'DEUDAS':
        return  '';
        break;

        case 'COSTCENTERS':
        return  '';
        break;

        case 'REPORTES':
        return '';
        break;

        case 'PERSONAL':
        return '';
        break;
    }

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
        $('#importe').val(parseFloat(p_actual) + parseFloat(igv));
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
        });
    }
}