@extends('layouts.app')

@section('content')

<?php
    date_default_timezone_set('America/Lima');
    $sdate = date("d") . "-" . date("m") . "-" . date("Y");
?>

<div class="col-md-10 col-md-offset-1">
    <div class="panel panel-default">
        <div class="panel-heading">
        	<div class="row">
        		<div class="col-md-4">
        			<h4 class="text-left">Ventas Realizadas hoy <?php echo $sdate; ?></h4>
        		</div>
        		<div class="col-md-3">
        			<div class="input-group">
					    <input type="text" id="search" class="form-control">
					    <span class="input-group-addon"><i class="icon-search"></i></span>
					</div>
        		</div>
        		<div class="col-md-1">
        			<a class="btn btn-primary btn-sm pull-right" onclick="window.open('{{ route('repDiario', ['tipo' => 1, 'fecha' => $sdate, 'fecha2' => $sdate, 'ordenado' => 'id', 'incluir' => 'true']) }}', null, 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,titlebar=no')">REP. HOY</a>
        		</div>
        		<div class="col-md-1">
        			<a class="btn btn-danger btn-sm pull-right" onclick="window.location.href = '{{ route('repDiario', ['tipo' => 2, 'fecha' => $sdate, 'fecha2' => $sdate, 'ordenado' => 'id', 'incluir' => 'true']) }}';"><i class="icon-download-alt"></i> PDF</a>
        		</div>
        		<div class="col-md-1">
        			<a class="btn btn-success btn-sm pull-right" onclick="window.location.href = '{{ route('repDiario', ['tipo' => 3, 'fecha' => $sdate, 'fecha2' => $sdate, 'ordenado' => 'id', 'incluir' => 'true']) }}';"><i class="icon-download-alt"></i> EXCEL</a>
        		</div>
        		<div class="col-md-2">
        			<a class="btnMenAlert btn btn-info btn-sm pull-right" data-target="#alertasModal" data-toggle="modal">MENSAJES DE ALERTA: <b id="malertas"><?=$alertas; ?></b></a>
        		</div>
        	</div>
        </div>
        <div class="panel-body">
            <div class="row">
				<div class="col-md-12">
					<table class="table table-responsive" id="Diario">
						<thead>
							<tr>
								<th>RECIBO</th>
								<th>FECHA</th>
								<th>TOTAL</th>
								<th>CLIENTE</th>
								<th>DNI</th>
								<th>DEUDA</th>
								<th>ESTADO</th>
								<th>DET.</th>
								<th class="anul">ANUL.</th>
							</tr>
						</thead>
						<tbody>
							@foreach($detalles as $detalle)
							<tr>
								<td>{{ $detalle->numrecibo }}</td>
								<td>{{ $detalle->fecha }}</td>
								<td>{{ number_format($detalle->monto, 2, '.', ',') }}</td>
								<td>{{ $detalle->nombres }} {{ $detalle->apellidop }} {{ $detalle->apellidom }}</td>
								<td>{{ $detalle->dni }}</td>
								<td>
									@if ($detalle->estado == 0)
										<font style="color: red; font-weight: bold;">ADEUDA</font> 
									@else 
										<font style="color: green; font-weight: bold;">CONFORME</font> 
									@endif
								</td>
								<td class="{{ $detalle->id }}">
									@if ($detalle->anulado == 0)
										<font style="color: red; font-weight: bold;">ANULADO</font> 
									@else 
										<font style="color: green; font-weight: bold;">GUARDADO</font> 
									@endif
								</td>
								<td>
									<a class="detalles btn btn-success btn-sm" data-id="{{ $detalle->id }}" data-recibo="{{ $detalle->numrecibo }}" data-cliente="{{ $detalle->nombres }} {{ $detalle->apellidop }} {{ $detalle->apellidom }}" data-toggle="modal" data-target="#detallesModal"><i class="icon-list"></i></a>
								</td>
								<td class="anul">
									@if ($detalle->anulado == 0)
										<a data-id="{{ $detalle->id }}" data-cambio="1" class="anul btnAnularRecibo btn btn-primary btn-sm">Recup.</i></a>
									@else 
										<a data-id="{{ $detalle->id }}" data-cambio="0" class="anul btnAnularRecibo btn btn-danger btn-sm">Anular</i></a>
									@endif									
								</td>
							</tr>
							@endforeach

							@if (count($detalles) == 0)
								<tr><td colspan="9" style="text-align: center;">No hay ventas registradas hoy. </td></tr>
							@endif
						</tbody>
					</table>

						
				</div>
			</div>
        </div>
    </div>
    <div id="detallesModal" class="modal fade" role="dialog">
    	<div class="modal-dialog modal-lg">
		    <!-- Modal content-->
		    <div class="modal-content">
		        <div class="modal-header">
		        	<div class="row">
		        		<div class="col-md-5">
		        			<h4 class="modal-title"><b style="color:red">Detalles de venta para el Recibo <b id="recibo"></b></b></h4>
		        		</div>
		        		<div class="col-md-7">
		        			<h4 class="modal-title"><b style="color:blue">Cliente: <b id="cliente"></b></b></h4>
		        		</div>
		        	</div>
		        </div>
		        <div class="modal-body">
		        	<table class="table table-responsive" id="tableDetalles">
		        		<thead>
		        			<tr>
			        			<th>CANTIDAD</th>
			        			<th>CONCEPTO</th>
			        			<th>DESCRIPCIÓN</th>
			        			<th>P.TOTAL</th>
			        			<th>IMPORTE</th>
			        			<th>SALDO</th>
			        		</tr>
		        		</thead>	
						<tbody id="tablaDetalles"></tbody>
						<tfoot>
		        			<tr>
			        			<th></th>
			        			<th></th>
			        			<th></th>
			        			<th>TOTAL</th>
			        			<th class="impi"></th>
			        			<th class="saldi"></th>
			        		</tr>
		        		</tfoot>
		        	</table>
		        </div>
		        <div class="modal-footer">
		        	<button type="button" class="btn btn-warning" data-dismiss="modal" onclick="$('#tableDetalles tbody tr').fadeOut();">Cerrar</button>
		        </div>
		    </div>
        </div>
	</div>
</div>

<div id="alertasModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-9">
                        <h4 class="modal-title"><b style="color:purple;"><i class="icon-warning-sign"></i> Mensajes</b></h4>
                    </div>
                    <div class="col-md-3">
                        <h4 class="modal-title"><b style="color:purple;">Fecha: <?php echo $sdate; ?></b></h4>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                @if(count($dias) != 0) 
            		<div class="row">
            			<div class="col-md-4">
            				<b style="color:red;">TIENES <b id="c_abiertas"><?=count($dias); ?></b> CAJA(S) ABIERTA(S)</b>
            			</div>
            			<div class="col-md-8">
            				@foreach($dias as $dia)
								<button data-op="0" id="<?=$dia['fecha'];?>" data-fecha="<?=$dia['fecha'];?>" data-deudadia="<?=$dia['total'];?>" class="btnCaj_ant btn btn-sm btn-warning"><?=$dia['fecha'];?></button>
            				@endforeach
            			</div>
            		</div>
            		<div id="caja0" class="row">
            			<div id="formPapeletas_ant" class="collapse">
            				<div class="col-md-5">
	            				<b style="color:green;">CANTIDAD VENDIDA: S/. <b id="mtotal_ant"></b></b>
	            			</div>
	            			<input type="hidden" id="fe_ant">
            				<div class="col-md-12"><p id="sms_ant" style="color:red;"></p></div>
							<form class="col-md-6">
								<div class="panel panel-success">
									<div class="panel-body">
								        <label>Papeleta 01</label>
								        <div class="row">
							        		<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">Número</span>
												    <input placeholder="Ingresar número" type="text" class="form-control input-sm" id="np1_ant">
												</div>
											</div>
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">S/.</span>
												    <input placeholder="Ingresar Monto" type="text" class="form-control input-sm" id="mp1_ant">
												</div>
											</div>
								        </div>
								    </div>
								</div>
								<div class="panel panel-success">
									<div class="panel-body">
									    <label>Papeleta 02</label>
									    <div class="row">
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">Número</span>
												    <input placeholder="Ingresar número" type="text" class="form-control input-sm" id="np2_ant">
												</div>
											</div>
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">S/.</span>
												    <input placeholder="Ingresar Monto" type="text" class="form-control input-sm" id="mp2_ant">
												</div>
											</div>
									    </div>
									</div>
								</div>
							</form>
							<form class="col-md-6">
								<div class="panel panel-success">
									<div class="panel-body">
										<label>Papeleta 03</label>
									    <div class="row">
											<div class="col-md-6">
												<div class="input-group">
													<span class="input-group-addon input-sm">Número</span>
												    <input placeholder="Ingresar número" type="text" class="form-control input-sm" id="np3_ant">
												</div>	
											</div>
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">S/.</span>
												    <input placeholder="Ingresar Monto" type="text" class="form-control input-sm" id="mp3_ant">
												</div>
											</div>
									    </div>
									</div>
								</div>
								<div class="panel panel-success">
									<div class="panel-body">
									    <label>Papeleta 04</label>
									    <div class="row">
											<div class="col-md-6">
												<div class="input-group">
													<span class="input-group-addon input-sm">S/.</span>
												    <input placeholder="Ingresar número" type="text" class="form-control input-sm" id="np4_ant">
												</div>	
											</div>
											<div class="col-md-6">
												<div class="input-group">
													<span class="input-group-addon input-sm">S/.</span>
												    <input placeholder="Ingresar Monto" type="text" class="form-control input-sm" id="mp4_ant">
												</div>	
											</div>
									    </div>
									</div>
								</div>
							</form>
							<div class="col-md-5 col-md-offset-5">
						    	<button type="button" id="regpapeletas_ant" data-fe="0" class="btn btn-default">Registrar</button>
							</div>
						</div>
					</div>
					<b style="color:blue;" id="caja00" class="hide"></b>
            	@else 
					<b style="color:blue;" id="cerrado_ant">NO TIENES CAJAS ABIERTAS DE DÍAS ANTERIORES</b>
            	@endif
            	<hr>
            	@if($caja[0]['estado'] == 0) 
            		<div id="caja01" class="row">
            			@if($totaldetotales[0]->ds != 0)
            			<div class="col-md-4">
            				<b style="color:red;">AÚN NO CIERRAS CAJA HOY</b>
            			</div>
            			@else
						<div class="col-md-4">
            				<b style="color:red;">NO HAY VENTAS ORDINARIAS HOY</b>
            			</div>
            			@endif
            			<div class="col-md-4">
            				<b style="color:green;">CANTIDAD VENDIDA: S/. <b id="mtotal"><?php if($totaldetotales[0]->ds == 0) echo '0.00'; else echo $totaldetotales[0]->ds; ?></b></b>
            			</div>
            			<div id="ParaCerrarCaja">
            				@if($totaldetotales[0]->ds != 0)
	            			<div class="col-md-2">
	            				<button class="btnRes btn btn-sm btn-success" data-toggle="collapse" data-target="#formPapeletas">CERRAR</button>
	            			</div>
	            			@else 
	            			<div class="col-md-4">
	            				<font size="2px"><i class="icon-check"></i> Si no hay ventas hoy, este apartado se eliminará automáticamente el día de mañana.</font>
	            				<font size="2px"><i class="icon-check"></i> Si tiene registros de ventas a terceros, el apartado no se eliminará.</font>
	            			</div>
	            			@endif
            			</div>
            		</div>

            		<div id="caja02" class="row">
            			<div id="formPapeletas" class="collapse">
            				<div class="col-md-12"><p id="sms" style="color:red;"></p></div>
							<form class="col-md-6">
								<div class="panel panel-success">
									<div class="panel-body">
								        <label for="mp1">Papeleta 01</label>
								        <div class="row">	
											<div class="col-md-6">
												<div class="input-group">
													<span class="input-group-addon input-sm">Número</span>
												    <input placeholder="Ingresar número" type="text" class="form-control input-sm" id="np1">
												</div>	
											</div>
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">S/.</span>
												    <input placeholder="Ingresar Monto" type="text" class="form-control input-sm" id="mp1">
												</div>	
											</div>
										</div>
							        </div>
							    </div>
							    <div class="panel panel-success">
									<div class="panel-body">
									    <label for="mp2">Papeleta 02</label>
									    <div class="row">
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">Número</span>
												    <input placeholder="Ingresar número" type="text" class="form-control input-sm" id="np2">
												</div>	
											</div>
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">S/.</span>
												    <input placeholder="Ingresar Monto" type="text" class="form-control input-sm" id="mp2">
												</div>
											</div>
									    </div>
									</div>
								</div>
							</form>
							<form class="col-md-6">
								<div class="panel panel-success">
									<div class="panel-body">
										<label for="mp3">Papeleta 03</label>
									    <div class="row">
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">Número</span>
												    <input placeholder="Ingresar número" type="text" class="form-control input-sm" id="np3">
												</div>
											</div>
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">S/.</span>
												    <input placeholder="Ingresar Monto" type="text" class="form-control input-sm" id="mp3">
												</div>
											</div>
									    </div>
									</div>
								</div>
							    <div class="panel panel-success">
									<div class="panel-body">
									    <label for="mp4">Papeleta 04</label>
									    <div class="row">
											<div class="col-md-6">
												<div class="input-group">
													<span class="input-group-addon input-sm">Número</span>
												    <input placeholder="Ingresar número" type="text" class="form-control input-sm" id="np4">
												</div>	
											</div>
											<div class="col-md-6">	
												<div class="input-group">
													<span class="input-group-addon input-sm">S/.</span>
												    <input placeholder="Ingresar Monto" type="text" class="form-control input-sm" id="mp4">
												</div>
											</div>
									    </div>
									</div>
								</div>
							</form>
							<div class="col-md-5 col-md-offset-5">
						    	<button type="button" id="regpapeletas" class="btn btn-default">Registrar</button>
							</div>
						</div>
					</div>
				@else 
					<b style="color:blue;">YA CERRASTE CAJA HOY</b>
            	@endif
            	<b style="color:blue;" id="caja03" class="hide"></b>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">ACEPTAR</button>
            </div>
        </div>
    </div>
</div>

@include('scripts.script_ventas_hoy')

@endsection