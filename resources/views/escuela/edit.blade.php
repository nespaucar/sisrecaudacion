@extends('casa')

@section('contentbody')
<script>
    header('{{ route('buscarescuela') }}', '{{ route('nuevaescuela') }}', 'ESCUELAS', 'escuelas', 5, '{{ csrf_field() }}');
    $(document).ready(function() {
        $('.{{ $escuela->facultad_id }}').attr('selected', 'selected');
    });
</script>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Editar Escuela <b>"{{ $escuela->nombre }}"</b></div>

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
                <form class="form-horizontal" action="{{ route('escuela.update', $escuela->id) }}}" method="PUT">
                    <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">Código</label>

                        <div class="col-md-6">
                            <input maxlength="10" id="codigo" type="text" class="form-control" name="codigo" value="{{ $escuela->codigo }}" readonly>

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
                            <input maxlength="50" id="nombre" type="text" class="form-control" name="nombre" value="{{ $escuela->nombre }}" autofocus>

                            @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                     <div class="form-group{{ $errors->has('facultad_id') ? ' has-error' : '' }}">
                        <label for="facultad_id" class="col-md-4 control-label">Facultad</label>

                        <div class="col-md-6">
                            <select name="facultad_id" id="facultad_id" class="form-control">
                                @foreach($facultades as $facultad)

                                <option class="{{ $facultad->id }}" value="{{ $facultad->id }}">{{ $facultad->nombre }}</option>

                                @endforeach
                            </select>

                            @if ($errors->has('facultad_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('facultad_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-success">
                                Editar Escuela
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
