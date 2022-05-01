@extends('casa')

@section('contentbody')

    <?php
        date_default_timezone_set('America/Lima');
        $sdate = date("d") . "-" . date("m") . "-" . date("Y");
    ?>

<script>
    header('', '', 'REPORTES', 'reportes', 5, '{{ csrf_field() }}');
    $( function() {
	    $( "#dia" ).datepicker();
	    $( "#di" ).datepicker();
	    $( "#df" ).datepicker();
	} );
</script>

@include('scripts.script_reportes')

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="row">
					<div class="col-md-1">
						<h4 class="panel-title">Reportes</h4>
					</div>
					<div class="col-md-11 text-right">
						<div class="col-md-2">
							<div class="col-md-12"><a onclick="opcionreporte('pccyf', 'pf', 'pempresa')" id="pf" style="background-color: #D5D5D5" class="btn btn-sm btn-default">Por Fecha</a></div>
						</div>
						<div class="col-md-3">
							<div class="col-md-12"><a onclick="opcionreporte('pf', 'pccyf', 'pempresa')" id="pccyf" class="btn btn-sm btn-default">Por Centr.Cost. y Fecha</a></div>
						</div>
						<div class="col-md-3">
							<div class="col-md-12"><a onclick="opcionreporte('pf', 'pempresa', 'pccyf')" id="pempresa" class="btn btn-sm btn-default">Servicios a Terceros</a></div>
						</div>
						<h4 class="panel-title" id="mensajeReporte" style="font-weight: bold;"></h4>
					</div>
				</div>
	    	</div>
			<div class="panel-body">
				<div class="form-horizontal" role="form">
					<div class="form-group">
						<div id="ap_intervalo">
							<label for="intervalo" class="control-label col-md-1">
								Intervalo
							</label>
							<div class="col-md-2">
								<select class="form-control input-sm" name="intervalo" id="intervalo">
									<option id="N" value="N">NORMAL</option>
									<option id="P" value="P">PERIODO</option>
								</select>
							</div>
						</div>
						<div id="opcion_fecha">
							<label for="tipo" class="control-label col-md-1">
								Tipo
							</label>
							<div class="col-md-2">
								<select class="form-control input-sm" name="tipo" id="tipo">
									<option id="D" value="D">DIARIO</option>
									<option id="M" value="M">MENSUAL</option>
									<option id="A" value="A">ANUAL</option>
								</select>
							</div>
							<div id="ap_incluir">
								<label for="incluir" class="control-label col-md-1">
									Incluir
								</label>
								<div class="col-md-2">
									<select class="form-control input-sm" name="incluir" id="incluir">
										<option id="ND" value="false">NADA</option>
										<option id="C" value="true">CONCEPTOS</option>
									</select>
								</div>
							</div>
							<label for="ordenar" class="control-label col-md-1">
								Ordenar
							</label>
							<div class="col-md-2">
								<select class="form-control input-sm" name="ordenar" id="ordenar">
									<option id="R" value="id">RECIBO</option>
								</select>
							</div>
						</div>
						<div id="opcion_concepto_cc" class="hide">
							<label for="op_costcenter" class="control-label col-md-2">
								CentroCosto
							</label>
							<div class="col-md-3">
								<select class="form-control input-sm" name="op_costcenter" id="op_costcenter">
									<option value="0">-- TODOS --</option>
									@foreach($centercosts as $centercost)
										<?=$centercost; ?> 
									@endforeach
									@if(count($centercosts) == 0)
										<option>No hay Centros de Costos Disponibles</option>
									@endif
								</select>
							</div>
							<label for="op_concepto" class="control-label col-md-1">
								Concepto
							</label>
							<div class="col-md-3">
								<select class="form-control input-sm" name="op_concepto" id="op_concepto">
									<option value="0">-- TODOS --</option>
									@foreach($conceptos as $concepto)
										<option value="{{ $concepto->id }}">{{ $concepto->descripcion }}</option>
										@foreach($concepto->tasas as $tasa)
											<option value="T;{{ $tasa->id }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $tasa->descripcion }}</option>
										@endforeach
									@endforeach
									@if(count($conceptos) == 0)
										<option>No hay Conceptos Disponibles</option>
									@endif
								</select>
							</div>
						</div>
					</div>
				</div>
				<hr>
				
				<form class="hide form-horizontal" role="form" id="_dia">
					<div class="form-group">
						<label for="dia" class="control-label col-md-1">
							DIA
						</label>
						<div class="col-md-3">
							<input class="form-control input-sm" id="dia" name="dia" type="text" value="" autofocus="autofocus">
						</div>
					</div>
				</form>

				<form class="hide form-horizontal" role="form" id="rango_dia">
					<div class="form-group">
						<label for="di" class="control-label col-md-1">
							INICIO
						</label>
						<div class="col-md-3">
							<input class="form-control input-sm" id="di" name="di" type="text" value="">
						</div>
						<label for="df" class="control-label col-md-1">
							FIN
						</label>
						<div class="col-md-3">
							<input class="form-control input-sm" id="df" name="df" type="text" value="">
						</div>
					</div>
				</form>

				<form class="hide form-horizontal" role="form" id="_mes">
					<div class="form-group">
						<label for="mes" class="control-label col-md-1">
							MES
						</label>
						<div class="col-md-2">
							<select class="form-control input-sm" id="mes" name="mes">
								<option value="01">ENERO</option>
								<option value="02">FEBRERO</option>
								<option value="03">MARZO</option>
								<option value="04">ABRIL</option>
								<option value="05">MAYO</option>
								<option value="06">JUNIO</option>
								<option value="07">JULIO</option>
								<option value="08">AGOSTO</option>
								<option value="09">SETIEMBRE</option>
								<option value="10">OCTUBRE</option>
								<option value="11">NOVIEMBRE</option>
								<option value="12">DICIEMBRE</option>
							</select>
						</div>
						<label for="mano" class="control-label col-md-1">
							AÑO
						</label>
						<div class="col-md-2">
							<select class="form-control input-sm" id="mano" name="mano">
								<option value="2017">2017</option>
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
								<option value="2021">2021</option>
								<option value="2021">2022</option>
								<option value="2021">2023</option>
								<option value="2021">2024</option>
							</select>
						</div>
					</div>
				</form>

				<form class="hide form-horizontal" role="form" id="rango_mes">
					<div class="form-group">
						<label for="mi" class="control-label col-md-1">
							M. INI.
						</label>
						<div class="col-md-2">
							<select class="form-control input-sm" id="mi" name="mi">
								<option value="01">ENERO</option>
								<option value="02">FEBRERO</option>
								<option value="03">MARZO</option>
								<option value="04">ABRIL</option>
								<option value="05">MAYO</option>
								<option value="06">JUNIO</option>
								<option value="07">JULIO</option>
								<option value="08">AGOSTO</option>
								<option value="09">SETIEMBRE</option>
								<option value="10">OCTUBRE</option>
								<option value="11">NOVIEMBRE</option>
								<option value="12">DICIEMBRE</option>
							</select>
						</div>
						<label for="ami" class="control-label col-md-1">
							AÑO
						</label>
						<div class="col-md-2">
							<select class="form-control input-sm" id="ami" name="ami">
								<option value="2017">2017</option>
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
								<option value="2021">2021</option>
								<option value="2021">2022</option>
								<option value="2021">2023</option>
								<option value="2021">2024</option>
							</select>
						</div>
						<label for="mf" class="control-label col-md-1">
							M. FIN.
						</label>
						<div class="col-md-2">
							<select class="form-control input-sm" id="mf" name="mf">
								<option value="01">ENERO</option>
								<option value="02">FEBRERO</option>
								<option value="03">MARZO</option>
								<option value="04">ABRIL</option>
								<option value="05">MAYO</option>
								<option value="06">JUNIO</option>
								<option value="07">JULIO</option>
								<option value="08">AGOSTO</option>
								<option value="09">SETIEMBRE</option>
								<option value="10">OCTUBRE</option>
								<option value="11">NOVIEMBRE</option>
								<option value="12">DICIEMBRE</option>
							</select>
						</div>
						<label for="amf" class="control-label col-md-1">
							AÑO
						</label>
						<div class="col-md-2">
							<select class="form-control input-sm" id="amf" name="amf">
								<option value="2017">2017</option>
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
								<option value="2021">2021</option>
								<option value="2021">2022</option>
								<option value="2021">2023</option>
								<option value="2021">2024</option>
							</select>
						</div>
					</div>
				</form>

				<form class="hide form-horizontal" role="form" id="_ano">
					<div class="form-group">
						<label for="ano" class="control-label col-md-1">
							AÑO
						</label>
						<div class="col-md-2">
							<select class="form-control input-sm" id="ano" name="ano">
								<option value="2017">2017</option>
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
								<option value="2021">2021</option>
								<option value="2021">2022</option>
								<option value="2021">2023</option>
								<option value="2021">2024</option>
							</select>
						</div>
					</div>
				</form>

				<form class="hide form-horizontal" role="form" id="rango_ano">
					<div class="form-group">
						<label for="ai" class="control-label col-md-1">
							INICIO
						</label>
						<div class="col-md-2">
							<select class="form-control input-sm" id="ai" name="ai">
								<option value="2017">2017</option>
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
								<option value="2021">2021</option>
								<option value="2021">2022</option>
								<option value="2021">2023</option>
								<option value="2021">2024</option>
							</select>
						</div>
						<label for="af" class="control-label col-md-1">
							FIN
						</label>
						<div class="col-md-2">
							<select class="form-control input-sm" id="af" name="af">
								<option value="2017">2017</option>
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
								<option value="2021">2021</option>
								<option value="2021">2022</option>
								<option value="2021">2023</option>
								<option value="2021">2024</option>
							</select>
						</div>
					</div>
				</form>	
				<hr>
				<div class="form-horizontal" role="form" id="p__dia">
					<div class="form-group">
						<div align="center" class="col-md-4">
							<button value="btnVIS" form="_dia" class="btn btn-primary btnR" type="button"><i class="icon-print"></i> IMPRIMIR</button>
						</div>
						<div align="center" class="col-md-4">
							<button value="btnPDF" form="_dia" class="btn btn-danger btnR" type="button"><i class="icon-download-alt"></i> PDF</button>
						</div>
						<div align="center" class="col-md-4">
							<button value="btnEXC" form="_dia" class="btn btn-success btnR" type="button"><i class="icon-download-alt"></i> EXCEL</button>
						</div>
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>

@endsection