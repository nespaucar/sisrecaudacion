<?php  
  if(Auth::guest()) {
      header(Route('login'));
  }
?>

@extends('casa')

@section('contentbody')

<script>
    header('{{ route('buscarfacultad') }}', '{{ route('nuevafacultad') }}', 'FACULTADES', 'facultades', 5, '{{ csrf_field() }}');
</script>

<div id="mantenimiento" class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>Código</th>
				<th>Nombre</th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach($facultades as $facultad)

			<tr id="{{ $facultad->id }}">
				<td>{{ $facultad->codigo  }}</td>
			    <td>{{ $facultad->nombre  }}</td>
			    <td><a href="{{ route('facultad.edit', $facultad->id) }}" class="btn btn-sm btn-info"><i class="icon-pencil text-center"></i></a></td>
			    <td><a data-bean="facultads" data-nombre="{{ $facultad->nombre  }}" data-id="{{ $facultad->id  }}" data-table="la facultad" data-toggle="modal" data-target="#deleteModal" class="eliminarBean btn btn-sm btn-warning"><i class="icon-remove text-center"></i></a></td>
			</tr>

			@endforeach

			<?php if(count($facultades) == 0){ 
				echo '<tr><td colspan="4" style="text-align: center">No hay facultades disponibles</td></tr>';
			} ?>
		</tbody>
	</table>
</div>

@endsection