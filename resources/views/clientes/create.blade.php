@extends('casa')

@section('contentbody')

<script>
    header('{{ route('buscarcliente') }}', '{{ route('nuevocliente') }}', 'CLIENTES', 'clientes', 4, '{{ csrf_field() }}');

    $(document).on("click", "#cr", function () { 
        $('#11').val('1');
        $('#22').val('');
        $('#apellidop').val('');
        $('#apellidop').removeAttr('readonly');
        $('#apellidom').val('');
        $('#apellidom').removeAttr('readonly');
        $('#esc').show();
    });

    $(document).on("click", "#al", function () { 
        $('#22').val('2');
        $('#11').val('');
        $('#apellidop').val('-');
        $('#apellidop').attr('readonly', 'readonly');
        $('#apellidom').val('-');
        $('#apellidom').attr('readonly', 'readonly');
        $('#esc').hide();
    });

    

</script>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Nuevo Cliente</div>

            <div class="panel-body">
            @if(Session::has('alerta_create'))
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="alert alert-info text-center alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{Session::get('alerta_create')}}
                        </div>
                    </div>                    
                </div>
            @endif
                <form class="form-horizontal" method="POST" action="{{ route('cliente.store') }}">
                    {{ csrf_field() }}

                     <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">Código</label>

                        <div class="col-md-6">
                            <input maxlength="10" id="codigo" type="text" class="form-control" name="codigo" value="{{ old('codigo') }}" autofocus>

                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
                        <label for="tipo" class="col-md-4 control-label">Tipo</label>

                        <div class="col-md-6">
                            <label class="radio-inline" id="cr"><input type="radio" id="11" value="1" name="tipo" checked>Alumno Interno</label>
                            <label class="radio-inline" id="al"><input type="radio" id="22" value="" name="tipo">Otro</label>

                            @if ($errors->has('tipo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tipo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('nombres') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-4 control-label">Nombres</label>

                        <div class="col-md-6">
                            <input maxlength="200" id="nombres" type="text" class="form-control" name="nombres" value="{{ old('nombres') }}" autofocus>

                            @if ($errors->has('nombres'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombres') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('apellidop') ? ' has-error' : '' }}">
                        <label for="apellidop" class="col-md-4 control-label">Apellido Paterno</label>

                        <div class="col-md-6">
                            <input maxlength="50" id="apellidop" type="text" class="form-control" name="apellidop" value="{{ old('apellidop') }}">

                            @if ($errors->has('apellidop'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellidop') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('apellidom') ? ' has-error' : '' }}">
                        <label for="apellidom" class="col-md-4 control-label">Apellido Materno</label>

                        <div class="col-md-6">
                            <input maxlength="50" id="apellidom" type="text" class="form-control" name="apellidom" value="{{ old('apellidom') }}">

                            @if ($errors->has('apellidom'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellidom') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('dni') ? ' has-error' : '' }}">
                        <label for="dni" class="col-md-4 control-label">DNI/RUC</label>

                        <div class="col-md-6">
                            <input maxlength="11" id="dni" type="text" class="form-control" name="dni" value="{{ old('dni') }}">

                            @if ($errors->has('dni'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('dni') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('escuela_id') ? ' has-error' : '' }}" id="esc">
                        <label for="escuela_id" class="col-md-4 control-label">Escuela</label>

                        <div class="col-md-6">
                            <select name="escuela_id" id="escuela_id" class="form-control">
                                @foreach($escuelas as $escuela)

                                <option value="{{ $escuela->id }}">{{ $escuela->nombre }}</option>

                                @endforeach
                            </select>

                            @if ($errors->has('escuela_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('escuela_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Registrar Cliente
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
