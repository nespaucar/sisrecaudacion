@extends('casa')

@section('contentbody')

@include('scripts.script_concepto')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">Nuevo Concepto</div>

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
                <form id="formRun" class="form-horizontal" method="POST" action="{{ route('concepto.store') }}">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-4 control-label">Descripción</label>

                        <div class="col-md-6">
                            <input maxlength="90" id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}" autofocus>

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

                                    <option value="{{ $cf->id }}">{{ $cf->codigo }}</option>

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

                                    <option value="{{ $cp->id }}">{{ $cp->codigo }}</option>

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
                        <button id="btnRun" class="btn btn-primary">
                            Registrar Concepto
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
