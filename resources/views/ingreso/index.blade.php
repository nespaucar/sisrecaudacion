@extends('casa')

@section('contentbody')

    <?php
        date_default_timezone_set('America/Lima');
        $sdate = date("d") . "-" . date("m") . "-" . date("Y");
    ?>

<script>
	header('', '', 'INGRESOS', 'ingresos', 5, '{{ csrf_field() }}');
</script>

@include('scripts.script_ingresos')

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-success">
			<div class="panel-heading">
	    		<div class="row">
					<div class="col-md-3">
						<h4 class="panel-title">Cliente</h4>
					</div>
					<div class="col-md-9 text-right">
						<h4 class="panel-title" id="mensajeCliente" style="color:red; font-weight: bold;"></h4>
					</div>
				</div>
	    	</div>
			<div class="panel-body">
 				<div class="form-horizontal">
					<div class="form-group">
						<form action="">
							<label for="codig" class="control-label col-md-1">
								Código
							</label>
							<div class="col-md-2">
								<input id="codig" name="codig" type="text" class="codigo form-control input-sm form-control-success" value="">
							</div>
							<div class="col-md-1">
								<button id="btnCliente" type="submit" class="btn btn-info btn-sm"><i class="icon-search"></i></button>
							</div>
						</form>
						<label for="nom" class="control-label col-md-1">
							Nombre
						</label>
						<div class="col-md-7">
							<input disabled id="nom" type="text" class="nombre form-control input-sm">
						</div>
					</div>
				</div>
				<div class="form-horizontal">
					<div class="form-group">
						<label for="escuela" class="control-label col-md-1">Esc.</label>
						<div class="col-md-6">
							<p id="escuela" class="form-control-static">-</p>
						</div>
						<label for="dni" class="control-label col-md-2">DNI/RUC</label>
						<div class="col-md-1">
							<p id="dni" class="form-control-static">-</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-3">
						<h4 class="panel-title">Centro de Costos</h4>
					</div>
					<div class="col-md-9 text-right">
						<h4 class="panel-title" id="mensajeCentroCostos" style="color:red; font-weight: bold;"></h4>
					</div>
				</div>
	    	</div>
			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<form action="">
							<div class="col-md-8">
								<input id="ccdescripcion" name="ccdescripcion" type="text" class="form-control input-sm form-control-success" value="Botón derecha y doble click para elegir un Centro de Costo" disabled="">
							</div>
							<div class="col-md-1">
								<a id="true" class="colapsar btn btn-success btn-sm"><i class="icon-collapse" id="colapsar"></i></a>
							</div>
							<div class="col-md-3">
								<div class="input-group">
								    <input type="text" id="search" class="form-control input-sm">
								    <span class="input-group-addon input-sm"><i class="icon-search"></i></span>
								</div>
							</div>
						</form>
					</div>
				</div>
				<ul class="file-tree file-list hidden" id="centercosts">
					@foreach($centercosts as $r)
						<?php echo $r; ?> 
					@endforeach
					@if (count($centercosts) == 0)
						<div>No hay CenterCosts disponibles</div>
					@endif
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-3">
						<h4 class="panel-title">Concepto</h4>
					</div>
					<div class="col-md-9 text-right">
						<h4 class="panel-title" id="mensajeConcepto" style="color:red; font-weight: bold;"></h4>
					</div>
				</div>
	    	</div>
			<div class="panel-body">
				<form action="">
					<div class="form-horizontal" role="form">
						<div class="form-group">
							<label for="Concepto" class="control-label col-md-1">
								Concepto
							</label>
							<div class="col-md-11">
								<select class="form-control input-sm" name="Concepto" id="Concepto">
									<option value="0">-- SELECCIONA CONCEPTO --</option>
								@foreach($conceptos as $concepto)
									<option value="{{ $concepto->id }}">{{ $concepto->descripcion }}</option>
								@endforeach
								@if (count($conceptos) == 0)
									<option>No hay Conceptos disponibles</option>
								@endif
								</select>
							</div>
						</div>
					</div>
					<div class="form-horizontal" role="form">
						<div class="form-group">
							<label for="Tasa" class="control-label col-md-1">
								Tasa
							</label>
							<div class="col-md-11">
								<select class="form-control input-sm" name="Tasa" id="Tasa">
									<option value="0">-- SELECCIONA TASA --</option>s
								</select>
							</div>
						</div>
					</div>
					<div class="form-horizontal" role="form">
						<div class="form-group">
							<label for="p_actual" class="control-label col-md-1">
								TUPA
							</label>
							<div class="col-md-1">
								<p class="form-control-static" id="p_actual"></p>
							</div>
							<label for="igv" class="control-label col-md-1">
								IGV
							</label>
							<div class="col-md-1">
								<p class="form-control-static" id="igv"></p>
							</div>
							<label for="descripcion" class="control-label col-md-1">
								Descr.
							</label>
							<div class="col-md-7">
								<input id="descripcion" type="text" class="form-control input-sm">
							</div>
						</div>
					</div>
					<div class="form-horizontal" role="form">
						<div class="form-group">
							<label for="p_real" class="control-label col-md-1">
								P.Real
							</label>
							<div class="col-md-2">
								<input id="p_real" type="text" class="form-control input-sm">
							</div>
							<label for="importe" class="control-label col-md-1">
								Imp.
							</label>
							<div class="col-md-2">
								<input id="importe" type="text" class="form-control input-sm">
							</div>
							<label for="cantidad" class="control-label col-md-1">
								Cantidad
							</label>
							<div class="col-md-1">
								<input id="cantidad" type="text" class="form-control input-sm" readonly="" value="1">
							</div>
							<div class="col-md-1">
								<form class="form-inline" role="form">
									<div class="form-group">
										<a class="btn btn-sm btn-danger" onclick="cantidad('+')"><i class="icon-chevron-sign-up"></i></a>
										<a class="btn btn-sm btn-danger" onclick="cantidad('-')"><i class="icon-chevron-sign-down"></i></i></a>
									</div>
								</form>
							</div>
							<div class="col-md-3">
								<button type="submit" class="btn btn-success btn-sm" id="btnConcepto"><i class="icon-plus"></i> AÑADIR A RECIBO</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<form action="" id="formdetalles" method="GET">
			<div class="panel panel-danger">
				<div class="panel-heading">
					<div class="row">
						<div class="col-md-2">
				    		<h4 class="panel-title">Detalles</h4>
				    	</div>
			    		<div class="col-md-10">
		    				<div class="form-horizontal" role="form">
								<div class="row">
									<div class="col-md-3">
										<h5>Fecha: <?php echo $sdate; ?></h5>
									</div>
									<div class="col-md-3">
										<select class="form-control input-sm" name="comprobante" id="comprobante">
											<option id="op_ba" value="0">BOLETA - ALUMNO</option>
											<option id="op_bt" class="hide" value="1">BOLETA - TERCERO</option>
											<option id="op_ft" class="hide" value="2">FACTURA - TERCERO</option>
										</select>
									</div>
									<div class="col-md-3">
										<div class="col-md-12">
											<input class="form-control input-sm" type="text" maxlength="20" name="numrecibo" id="numrecibo" placeholder="NUM RECIBO" value="<?php if($sequence[0]->sequence != "0") { echo $sequence[0]->sequence; } ?>" readonly="readonly" maxlength="6">
										</div>
									</div>
									<div class="col-md-2">
										<a href="{{ route('ventas_hoy') }}" id="btnVentasHoy" class="btn btn-primary btn-sm">VENTAS DE HOY</a>
									</div>
									<div class="col-md-1">
										<button type="submit" id="btnGuardarDetalles" class="btn btn-success btn-sm"><i class="icon-save"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
				<div class="panel-body">
					<div class="row hide" id="apartado_tercero">
						<label for="nrovoucher" class="control-label col-md-2">
							Nº Voucher
						</label>
						<div class="col-md-4">
							<input type="text" name="nrovoucher" id="nrovoucher" class="form-control input-sm">
						</div>
						<label for="nrocomprobante" class="control-label col-md-2">
							Nº Comprobante
						</label>
						<div class="col-md-4">
							<input type="text" name="nrocomprobante" id="nrocomprobante" class="form-control input-sm">
						</div>
						<hr>
					</div>
					<div class="row">
						<label for="nombre" class="control-label col-md-1">
							Cliente
						</label>
						<div class="col-md-7">
							<input disabled type="text" class="nombre form-control input-sm">
						</div>
						<label for="codigo" class="control-label col-md-1">
							Código
						</label>
						<div class="col-md-3">
							<input disabled type="text" class="codigo form-control input-sm">
						</div>
						<hr>
						<label for="centro_costos" class="control-label col-md-3">
							Centro de Costos
						</label>
						<div class="col-md-9">
							<input disabled type="text" class="centro_costos form-control input-sm" value="Debes Elegir un Centro de Costos">
						</div>
						<div id="mantenimiento" class="table-responsive col-md-12">
						<hr>
						<h4 class="panel-title text-center" id="alerta_detalles" style="color:red; font-weight: bold;"></h4>
							<table class="table" id="tableDetalle">
								<thead>
									<tr>
										<th>Cant.</th>
										<th>Conc.</th>
										<th>Descr.</th>
										<th>Tot.</th>
										<th>Impor.</th>
										<th>Saldo</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th></th>
										<th></th>
										<th>TOTAL</th>
										<th class="toti">0.00</th>
										<th class="impi">0.00</th>
										<th class="saldi">0.00</th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
			<!--inputs escondidos-->
			<input type="hidden" name="id_cliente_oculto" id="id_cliente_oculto" value="">
			<input type="hidden" name="cant_detalles" id="cant_detalles" value="0"/>
			<input type="hidden" name="centro_costos" id="centro_costos" value=""/>
			<!---->
		</form>
	</div>
</div>

<!-- Modal Alerta de impresion -->

<div id="reimprimirModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">
      <!-- Modal content-->
      <div class="modal-content">
          <div class="modal-header">
              <div class="col-md-12">
                <h3 class="modal-title"><b style="color:green;"><i class="icon-upload"></i> ¡Cuidado!</b></h3>
            </div>
          </div>
          <form role="form">
            <div class="modal-body">
              <div id="cuerpoReimprModal"></div>
              <h2 style="color:orange; text-align: center">¿Qué deseas hacer ahora?</b></h2><br>
            </div>
            <div class="modal-footer">
              <a class="btn btn-danger" id="reimpNumReciboSig">Error de impresión, Reimprimir.</a>
              <a class="btn btn-success" id="noReimprimir" onclick="noreimprimir()" data-dismiss="modal">Todo es Correcto, Continuar.</a>
            </div>
          </form>
      </div>
    </div>
</div>

@endsection