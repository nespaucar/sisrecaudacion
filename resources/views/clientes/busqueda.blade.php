@extends('casa')

@section('contentbody')

@include('scripts.script_cliente')

<div class="row">
	<div class="col-md-12">
		<b style="color: blue">Resultado de la búsqueda: {{$search}}</b>
	</div>
</div>
<hr />

<div id="mantenimiento" class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>Código</th>
				<th>DNI</th>
				<th>Nombres</th>
				<th>Tipo</th>
				<th>Facultad</th>
				<th></th>
				<th></th>
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
			    <td><a data-cliente="{{ $cliente->nombres . ' ' . $cliente->apellidop . ' ' . $cliente->apellidom  }}" data-id="{{ $cliente->id }}" data-toggle="modal" data-target="#detallesModal" data-buscar='T' class="detalles btn btn-sm btn-success"><i class="icon-list text-center"></i></a></td>
			    <td><a href="{{ route('cliente.edit', $cliente->id) }}" class="btn btn-sm btn-info"><i class="icon-pencil text-center"></i></a></td>
			    <td><a data-table="el cliente" data-bean="clientes" data-nombre="{{ $cliente->nombres . ' ' . $cliente->apellidop . ' ' . $cliente->apellidom  }}" data-id="{{ $cliente->id }}" data-toggle="modal" data-target="#deleteModal" class="eliminarBean btn btn-sm btn-warning"><i class="icon-remove text-center"></i></a></td>
			</tr>

			@endforeach

			@if (count($clientes) == 0)
				<tr><td colspan="8" style="text-align: center">No se encontraron clientes </td></tr>
			@endif
		
		</tbody>
	</table>
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
						    <input type="text" id="search" class="form-control">
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

@endsection