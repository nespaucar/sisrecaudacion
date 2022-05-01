@extends('casa')

@section('contentbody')
<script>
    $(document).ready(function() {
        $('#financialclassifier_id .{{ $concepto->financialclassifier_id }}').attr('selected', 'selected');
        $('#budgetclassifier_id .{{ $concepto->budgetclassifier_id }}').attr('selected', 'selected');
    });
</script>

@include('scripts.script_concepto')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Editar Concepto <b>"{{ $concepto->descripcion }}"</b></div>

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
                <form id="formRun" class="form-horizontal" action="{{ route('concepto.update', $concepto->id) }}}" method="PUT">
                    <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-4 control-label">Descripción</label>

                        <div class="col-md-6">
                            <input maxlength="80" id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $concepto->descripcion }}">

                            @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('financialclassifier_id') ? ' has-error' : '' }}">
                        <label for="financialclassifier_id" class="col-md-4 control-label">Clasificador Financiero</label>

                        <div class="col-md-6">
                            <div class="cc1">
                                <select name="financialclassifier_id" id="financialclassifier_id" class="form-control">
                                    @foreach($cfs as $cf)

                                    <option class="{{ $cf->id }}" value="{{ $cf->id }}">{{ $cf->codigo }}</option>

                                    @endforeach
                                </select>
                            </div>

                            <input id="cfn" type="text" class="hide form-control" name="cfn" value="0">

                            <label class="checkbox-inline"><input id="cfncbx" type="checkbox" value="1">Nuevo</label>

                            @if ($errors->has('financialclassifier_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('financialclassifier_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('budgetclassifier_id') ? ' has-error' : '' }}">
                        <label for="budgetclassifier_id" class="col-md-4 control-label">Clasificador Presupuestal</label>

                        <div class="col-md-6">

                            <div class="cc2">
                                <select name="budgetclassifier_id" id="budgetclassifier_id" class="form-control">
                                    @foreach($cps as $cp)

                                    <option class="{{ $cp->id }}" value="{{ $cp->id }}">{{ $cp->codigo }}</option>

                                    @endforeach
                                </select>
                            </div>

                            <input id="cpn" type="text" class="hide form-control" name="cpn" value="0">

                            <label class="checkbox-inline"><input id="cpncbx" type="checkbox" value="1">Nuevo</label>

                            @if ($errors->has('budgetclassifier_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('budgetclassifier_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </form>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button id="btnRun" class="btn btn-success">
                            Editar Concepto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
