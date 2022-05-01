@extends('casa')

@section('contentbody')
<script>
    header('{{ route('buscarfacultad') }}', '{{ route('nuevafacultad') }}', 'FACULTADES', 'facultades', 5, '{{ csrf_field() }}');
</script>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Editar Facultad <b>"{{ $facultad->nombre }}"</b></div>

            <div class="panel-body">
            @if(Session::has('alerta_edit'))
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="alert alert-info text-center alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{Session::get('alerta_edit')}}
                        </div>
                    </div>                    
                </div>
            @endif
                <form class="form-horizontal" action="{{ route('facultad.update', $facultad->id) }}}" method="PUT">
                    <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">Código</label>

                        <div class="col-md-6">
                            <input maxlength="10" id="codigo" type="text" class="form-control" name="codigo" value="{{ $facultad->codigo }}" readonly>

                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-4 control-label">Nombre</label>

                        <div class="col-md-6">
                            <input maxlength="90" id="nombre" type="text" class="form-control" name="nombre" value="{{ $facultad->nombre }}" autofocus>

                            @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-success">
                                Editar Facultad
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
