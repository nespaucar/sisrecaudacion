@extends('casa')

@section('contentbody')
<script>header('{{ route('buscarescuela') }}', '{{ route('nuevaescuela') }}', 'ESCUELAS', 'escuelas', 5, '{{ csrf_field() }}');</script>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Nueva Escuela</div>

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
                <form class="form-horizontal" method="POST" action="{{ route('escuela.store') }}">
                    {{ csrf_field() }}

                     <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">Código</label>

                        <div class="col-md-6">
                            <input maxlength="6" id="codigo" type="text" class="form-control" name="codigo" 
                            <?php if(isset( $max )) { ?>
                                value="{{ $max[0]->max }}" 
                            <?php } else { ?>
                                value="{{ old('codigo') }}" 
                            <?php } ?>
                            readonly>

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
                            <input maxlength="90" id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" autofocus>

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

                                <option value="{{ $facultad->id }}">{{ $facultad->nombre }}</option>

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
                            <button type="submit" class="btn btn-primary">
                                Registrar Escuela
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
