@extends('casa')

@section('contentbody')

    <?php
        date_default_timezone_set('America/Lima');
        $sdate = date("d") . "-" . date("m") . "-" . date("Y");
    ?>

@include('scripts.script_costcenters')

@if(Session::has('alerta'))
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="alert alert-info text-center alert-dismissable">
            	<button type="button" class="close" data-dismiss="alert">&times;</button>
            	{{Session::get('alerta')}}
            </div>
        </div>                    
    </div>
@endif

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-3">
						<h4 class="panel-title">Centro de Costos</h4>
					</div>
					<div class="col-md-9 text-right">
						<h4 class="panel-title" id="mensajeBean" style="color:red; font-weight: bold;"></h4>
					</div>
				</div>
	    	</div>
			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<form action="">
							<div class="col-md-2">
								<!-- hidden --><input id="id" name="id" type="hidden" value="SID">
								<input id="codigo" name="codigo" type="text" class="form-control input-sm form-control-success" disabled="" placeholder="CODIGO">
						    </div>
							<div class="col-md-5">
								<input id="nombre" name="nombre" type="text" class="form-control input-sm form-control-success" disabled="" placeholder="NOMBRE">
							</div>
							<div class="col-md-3" id="centercosts_select">
								<select id="padre" name="padre" class="form-control input-sm form-control-success" disabled="">
									<option value="0">NUEVO</option>
									@foreach($padres as $padre)
										<?php echo $padre; ?> 
									@endforeach

									@if (count($padres) == 0)
										<option> No hay Costcenters <option>
									@endif
								</select>
							</div>
							<div class="col-md-1">
								<form class="form-inline" role="form">
									<div class="form-group">
										<a id="nuevo_cc" class="btn btn-info btn-sm" title="NUEVO"><i class="icon-plus"></i></a>
										<a id="conforme_cc" class="btn btn-success btn-sm nue" title="CONFIRMAR"><i class="icon-check"></i></a>
									</div>
								</form>
							</div>
							<div class="col-md-1">
								<form class="form-inline" role="form">
									<div class="form-group">
										<a id="editar_cc" title="EDITAR" class="btn btn-warning btn-sm hidden"><i class="icon-edit"></i></a>
										<a data-bean="cost_centers" data-table="el centro de costos" id="eliminarBean" title="ELIMINAR" class="eliminarBean btn btn-danger btn-sm hidden" data-toggle="modal" data-target="#deleteModal"><i class="icon-remove"></i></a>
									</div>
								</form>
							</div>
						</form>
					</div>
				</div>
				<ul class="file-tree file-list" id="centercosts">
					@foreach($centercosts as $r)
						<?php echo $r; ?> 
					@endforeach

					@if (count($centercosts) == 0)
						<b style="color:red">No hay CostCenters Disponibles</b>
					@endif
				</ul>
			</div>
		</div>
	</div>
</div>

@endsection