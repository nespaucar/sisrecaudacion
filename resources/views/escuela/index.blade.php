@extends('casa')

@section('contentbody')

<script>
    header('{{ route('buscarescuela') }}', '{{ route('nuevaescuela') }}', 'ESCUELAS', 'escuelas', 5, '{{ csrf_field() }}');
</script>

<div id="mantenimiento" class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>Código</th>
				<th>Nombre</th>
				<th>Facultad</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach($escuelas as $escuela)

			<tr id="{{ $escuela->id }}">
				<td>{{ $escuela->codigo  }}</td>
			    <td>{{ $escuela->nombre  }}</td>
			    <td>{{ $escuela->nombrefacu }}</td>
			    <td><a href="{{ route('escuela.edit', $escuela->id) }}" class="btn btn-sm btn-info"><i class="icon-pencil text-center"></i></a></td>
			    <td><a data-bean="escuelas" data-table="la escuela" data-nombre="{{ $escuela->nombre }}" data-id="{{ $escuela->id }}" data-toggle="modal" data-target="#deleteModal" class="eliminarBean btn btn-sm btn-warning"><i class="icon-remove text-center"></i></a></td>
			</tr>

			@endforeach

			@if (count($escuelas) == 0)
				<tr><td colspan="5" style="text-align: center">No hay escuelas disponibles</td></tr>
			@endif
		
		</tbody>
	</table>
</div>

@endsection