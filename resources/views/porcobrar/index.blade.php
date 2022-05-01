@extends('casa')

@section('contentbody')

@include('scripts.script_porcobrar')

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

<div id="mantenimiento" class="table-responsive">
	<table class="table" id="tblDeudas">
		<thead>
			<tr>
				<th>Código</th>
				<th>DNI</th>
				<th>Nombres</th>
				<th>Tipo</th>
				<th>Escuela</th>
				<th>Cantidad</th>
				<th>Saldo</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach($clientes as $cliente)

			<tr id="{{ $cliente->id }}">
				<td>{{ $cliente->codigo  }}</td>
			    <td>{{ $cliente->dni  }}</td>
			    <td>{{ $cliente->nombres . ' ' . $cliente->apellidop . ' ' . $cliente->apellidom  }}</td>
			    <td>  	@if($cliente->tipo == 1) 
			    			Alumno Interno
			    		@else
			    			Otro
			    		@endif
			    </td>
			    <td>{{ $cliente->nombreescu  }}</td>
			    <td>{{ $cliente->cant_deudas  }}</td>
			    <td>{{ number_format($cliente->total_deudas, 2, '.', ',') }}</td>
			    <td><a data-cliente="{{ $cliente->nombres . ' ' . $cliente->apellidop . ' ' . $cliente->apellidom  }}" data-id="{{ $cliente->id }}" data-toggle="modal" data-target="#detallesModal" class="detalles btn btn-sm btn-success"><i class="icon-list text-center"></i></a></td>
			</tr>

			@endforeach

			@if (count($clientes) == 0)
				<tr><td colspan="8" style="text-align: center">No hay clientes con deudas Pendientes</td></tr>
			@endif
		
		</tbody>
	</table>
</div>
<div class="row text-center">
	<div class="col-md-12">
		{{ $clientes->links() }}
	</div>
</div>

<div id="detallesModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
	    <!-- Modal content-->
	    <div class="modal-content">
	        <div class="modal-header">
	        	<div class="row">
	        		<div class="col-md-9">
	        			<h4 class="modal-title"><b style="color:blue;">Historial de ventas de: <b style="color:green;" id="cliente"></b></b></h4>
	        		</div>
	        		<div class="col-md-3">
	        			<div class="input-group">
						    <input type="text" class="search form-control">
						    <span class="input-group-addon"><i class="icon-search"></i></span>
						</div>
	        		</div>
	        	</div>
	        </div>
	        <div class="modal-body">
	        	<div id="Historial"></div>
	        	<div id="Alerta"></div>
	        </div>
	        <div class="modal-footer">
	        	<button type="button" class="btn btn-warning" data-dismiss="modal" onclick="$('#tableDetalles tbody tr').fadeOut();">Cerrar</button>
	        </div>
	    </div>
    </div>
</div>

<div id="abonarModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
	    <!-- Modal content-->
	    <div class="modal-content">
	        <div class="modal-header">
	        	<div class="row">
	        		<div class="col-md-12">
	        			<h4 class="modal-title"><b style="color:blue;">Abonar Recibo: <b style="color:green;" id="rec_deuda"></b></b></h4>
	        		</div>
	        	</div>
	        </div>
	        <div class="modal-body">
	        	<div id="FormDeuda">
					<div class="form-horizontal" role="form">
					    <div class="form-group">
					        <label for="m_Total" class="col-md-5 control-label">Saldo Total</label>
					        <div class="col-md-7">
					            <b style="color: purple">S/. <b id="m_Total"></b></b>
					        </div>
					    </div>
					    <div class="form-group">
					    	<input type="hidden" id="id_table">
					        <label for="m_Abono" class="col-md-5 control-label">Abono</label>
					        <div class="col-md-7">
					        	<div class="input-group">
							    	<input type="text" class="form-control" id="m_Abono" placeholder="Ingresa Monto" autofocus="">
							    	<span class="input-group-addon">S/.</span>
							    </div>
					        </div>
					    </div>
					    <div class="form-group">
					        <div class="col-md-offset-4 col-md-4">
					            <a id="btnAbonar" class="btn btn-default">Abonar</a>
					        </div>
					    </div>
					    <div id="al_sms_Abono" class="form-group hide">
					        <p class="col-md-12 control-label" id="sms_Abono" class="text-center" style="color:green"></p>
					    </div>
					</div>
	        	</div>
	        	<div id="alertaFormDeuda"></div>
	        </div>
	        <div class="modal-footer">
	        	<button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
	        </div>
	    </div>
    </div>
</div>

@endsection