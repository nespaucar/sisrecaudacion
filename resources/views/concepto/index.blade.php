@extends('casa')

@section('contentbody')

<script>
    header('{{ route('buscarconceptos') }}', '{{ route('nuevoconcepto') }}', 'CONCEPTOS', 'conceptos', 5, '{{ csrf_field() }}');
</script>

<div id="mantenimiento" class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>Cls.F.</th>
				<th>Cls.P.</th>
				<th>Desc.</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach($conceptos as $concepto)

			<tr id="{{ $concepto->id }}">
				<td>{{ $concepto->fc  }}</td>
			    <td>{{ $concepto->bc  }}</td>
			    <td>{{ $concepto->descripcion }}</td>
			    <td><a href="{{ route('concepto.edit', $concepto->id) }}" class="btn btn-sm btn-info"><i class="icon-pencil text-center"></i></a></td>
			    <td><a data-table="el concepto" data-bean="conceptos" data-nombre="{{ $concepto->descripcion }}" data-id="{{ $concepto->id }}" data-toggle="modal" data-target="#deleteModal" class="eliminarBean btn btn-sm btn-warning"><i class="icon-remove text-center"></i></a></td>
			</tr>

			@endforeach

			@if (count($conceptos) == 0)
				<tr><td colspan="5" style="text-align: center;">No hay conceptos registrados </td></tr>
			@endif
		
		</tbody>
	</table>
</div>
<div class="row text-center">
	<div class="col-md-12">
		{{ $conceptos->links() }}
	</div>
</div>

@endsection