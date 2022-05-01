@extends('casa')

@section('contentbody')

<script>
	header('', '#', 'PERSONAL', 'personal', 5, '{{ csrf_field() }}');
</script>

@include('scripts.script_personal')

<style>
	.spn_form_personal {
		color: red;
		font-size: 12px;
	}

	.spn_form_personal_usuario {
		color: blue;
		font-size: 12px;
	}
</style>

<div id="mantenimiento" class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th style="width: 45%">DNI, Nombre, Dirección, Teléfono y Email</th>
				<th style="width: 25%">Tipo</th>
				<th style="width: 20%">Estado</th>
				<th style="width: 5%"></th>
				<th style="width: 5%"></th>
			</tr>
		</thead>
		<tbody>
			@foreach($personal as $persona)
				<tr id="{{ $persona->id }}">
					<td>
						<i class="icon-check"></i>  <font id="dni{{ $persona->id }}">{{ $persona->dni }}</font> <br> 
						<i class="icon-check"></i>  <font id="nom{{ $persona->id }}">{{ $persona->nombres}}</font> <font id="app{{ $persona->id }}"> {{ $persona->apellidop}} </font> <font id="apm{{ $persona->id }}"> {{$persona->apellidom  }}</font> <br> 
						<i class="icon-check"></i>  <font id="dir{{ $persona->id }}">{{ $persona->direccion }}</font> <br> 
						<i class="icon-check"></i>  <font id="tel{{ $persona->id }}">{{ $persona->telefono }}</font> <br> <i class="icon-check"></i>  <font id="email{{ $persona->id }}">{{ $persona->email }}</font></td>
				    <td>
				    	<font id="tipo{{ $persona->id }}">
					    	@if($persona->type == 1)
					    		SUPER ADMINISTRADOR
					    	@elseif($persona->type == 2)
								ADMINISTRADOR
							@elseif($persona->type == 3)
								PERSONAL ORDINARIO
					    	@endif
					    </font>
				    </td>
				    <td>
				    	@if($persona->estado == 1)
				    		@if($persona->id == Auth::user()->id)
				    			<input type="button" class="btn btn-default"  value="YO">
				    		@else
				    			@if($persona->type == 1)
									<input id="btn{{ $persona->id }}" type="button" class="btn btn-primary" value="HABIL">
								@else
									<input id="btn{{ $persona->id }}" type="button" class="btn btn-success hab" data-type="{{ $persona->estado }}" data-id="{{ $persona->id }}" value="HABIL" onclick="hab('{{ $persona->id }}')">
								@endif
				    		@endif
				    	@else
							<input id="btn{{ $persona->id }}" type="button" class="btn btn-danger hab" data-type="{{ $persona->estado }}" data-id="{{ $persona->id }}" value="INHAB" onclick="hab('{{ $persona->id }}')">
				    	@endif
				    </td>
				    <td><a id="edtReg{{ $persona->id }}" href="#" data-toggle="modal" data-target="#modalpersonal" data-id="{{ $persona->id  }}" data-tipo="{{ $persona->type  }}" data-estado="{{ $persona->estado  }}" class="btn btn-sm btn-info edtReg"><i class="icon-pencil text-center"></i></a></td>
				    @if($persona->id != Auth::user()->id)
				    <td><a data-bean="personal" data-nombre="{{ $persona->nombres . ' ' . $persona->apellidop . ' ' . $persona->apellidom  }}" data-id="{{ $persona->id  }}" data-table="al trabajador" data-toggle="modal" data-target="#deleteModal" class="eliminarBean btn btn-sm btn-warning"><i class="icon-remove text-center"></i></a></td>
				    @else
				    <td></td>
				    @endif
				</tr>
			@endforeach
				<?php if(count($personal)  == 0){ 
					echo '<tr><td colspan="4" style="text-align: center">No hay Personal disponibles</td></tr>';
				} ?>
		</tbody>
	</table>
</div>

<!-- Modal -->
<div class="modal fade" id="modalpersonal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close"><font id="cargando"></font></button>
				<h4 class="modal-title"><font id="titulomodalpersonal" style="color: blue"></font></h4>
			</div>
			<div class="modal-body">
				<div id="formulario_personal">
					<form action="" class="form-horizontal" role="form">
						<div class="form-group">
						    <div class="col-sm-6">
						      	<input name="dni" id="dni" maxlength="8" placeholder="INGRESA DNI" class="input-sm form-control concatuser solo-numero" type="text" autofocus="">
						      	<span class="spn_form_personal" id="spn_dni"></span>
						    </div>
						    <div class="col-sm-6">
						      	<input name="telefono" id="telefono" maxlength="9" placeholder="INGRESA TELEFONO" class="input-sm form-control solo-numero" type="text" autofocus="">
						      	<span class="spn_form_personal" id="spn_tel">Telefono requerido, solo 9 caracteres.</span>
						    </div>
						</div>
						<div class="form-group">
						    <div class="col-sm-4">
						      	<input name="nombres" id="nombres" placeholder="NOMBRES" class="input-sm form-control concatuser" type="text">
						      	<span class="spn_form_personal" id="spn_nom">Nombre requerido, max 50 car.</span>
						    </div>
						    <div class="col-sm-4">
						      	<input name="apellidop" id="apellidop" placeholder="AP. PATERNO" class="input-sm form-control concatuser" type="text">
						      	<span class="spn_form_personal" id="spn_apm">Apellidos Requeridos, max 50 car. c/u</span>
						    </div>
						    <div class="col-sm-4">
						      	<input name="apellidom" id="apellidom" placeholder="AP. MATERNO" class="input-sm form-control concatuser" type="text">
						    </div>
						</div>
						<div class="form-group">
						    <div class="col-sm-6">
						      	<input name="direccion" id="direccion" placeholder="INGRESA DIRECCION" class="input-sm form-control" type="text">
						      	<span class="spn_form_personal" id="spn_dir">Dirección Requerida.</span>
						    </div>
						    <div class="col-sm-6">
						      	<input name="email" id="email" placeholder="INGRESA EMAIL" class="input-sm form-control" type="email">
						      	<span class="spn_form_personal" id="spn_email"></span>
						    </div>
						</div>
						<div class="form-group">
							<div class="col-sm-3"></div>
						    <div class="col-sm-7">
						      	<select name="tipo" id="tipo" class="input-sm form-control">
									<option value="1">SUPER ADMINISTRADOR</option>
									<option value="2">ADMINISTRADOR</option>
									<option value="3">PERSONAL ORDINARIO</option>
								</select>
						    </div>
						</div>
						<hr>
						<div class="form-group">
							<label for="usuario" class="col-md-1">User</label>
						    <div class="col-sm-5">
						      	<input name="usuario" id="usuario" class="input-sm form-control" type="text" disabled="disabled">
						      	<span class="spn_form_personal_usuario">Usuario Automático.</span>
						    </div>
						    <label for="usuario" class="col-md-1">Pass</label>
						    <div class="col-sm-5">
						      	<input value="admin" name="pass" id="pass" class="input-sm form-control" type="password" disabled="disabled">
						      	<span class="spn_form_personal_usuario">Contraseña por defecto: admin</span>
						    </div>
						</div>
					</form>
				</div>
				<div id="mensaje_personal" class="hide">
					- <font id="mens_01"></font><br><br>
					- La contraseña por defecto es 'admin'. <br><br>
					- El usuario por defecto es 'HABILITADO'. 
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
				<button id="aceptarmodalpersonal" data-funcion="" type="button" class="btn btn-success">Aceptar</button>
			</div>
		</div>
	</div>
</div>
<script>
	
</script>
@endsection
