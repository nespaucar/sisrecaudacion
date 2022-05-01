@include('scripts.script_repGeneral')
<?php 
	$num = num($data, 40);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>ReporteMensual-<?= $date;?></title>
		
		<link rel="stylesheet" href="{{ asset('css/repDiario.css') }}">
		@if($data == '')
			<style>
				body {
					font-size: 12px;
				}
			</style>
		@endif
		<style>
		    #footer { position: fixed; left: 0px; bottom: -250px; right: 0px; height: 340px; }
		 </style>
	</head>
	<body>
		<div id="footer">
			<div class="fdizquierda">
				<div class="footerdivs">
					<font align="center">_______________________________<br>Ernesto Raúl Ríos Damián<br> <b>Recaudador</b></font>
				</div>
			</div>
			
			<div class="fdderecha">
				<div class="footerdivs">
					<font align="center">_______________________________<br>Ing. Luis Alberto Llontop Cumpa<br> <b>Jefe de Oficina de Administración</b></font>
				</div>
			</div>

			<div class="fdcentro">
				<div class="footerdivs">
					<font align="center">_______________________________<br>CPC. Bertha Elizabeth Delgado Posadas<br> <b>Contadora</b></font>
				</div>
			</div>
		</div>
		<div id="content" class="col-md-12">
			<div class="box">
				<div class="box-header" style="text-align: center;">
					<font>UNIVERSIDAD NACIONAL PEDRO RUIZ GALLO</font><br>
					<font>FACULTAD DE INGENIERIA CIVIL, SISTEMAS Y DE ARQUITECTURA - OFICINA DE ADMINISTRACION</font><br>
					<font>INFORME DE RECURSOS DIRECTAMENTE RECAUDADOS - LAMBAYEQUE</font><br>
					<font>CONSOLIDADO MENSUAL DE INGRESOS</font><br>
				</div>
				<br>
				<div class="box-header" style="text-align: left;">
					<div style="text-align: left;"><font><b>Fecha: <?=  $date; ?></b></font></div>
				</div>

				<div class="box-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<td 
									@if($data != '')
										style="width: 55px; text-align: center;"
									@else
										style="width: 33%; text-align: center;"
									@endif
								>FECHA</td>
								<td 
									@if($data != '')
										style="width: 55px; text-align: center;"
									@else
										style="width: 33%; text-align: center;"
									@endif
								>N°REC.</td>
								@if($data != '')
									@foreach($data as $concepto)
										<td style="text-align: center;"><?php echo ordenarPalabra($concepto->descripcion, $num); ?></td>
									@endforeach
								@endif
								<td style="text-align: center;">TOTAL</td>
							</tr>
							@if($data != '')
								<tr>									
									<td style="text-align: center;">C.FIN</td>
									<td></td>
									@foreach($data as $concepto)
										<td style="text-align: center;"><?php echo ordenarPalabra($concepto->fc, $num); ?></td>
									@endforeach
									<td></td>
								</tr>
								<tr>
									<td style="text-align: center;">C. PR</td>
									<td style="text-align: center;"></td>
									@foreach($data as $concepto)
										<td style="text-align: center;"><?php echo ordenarPalabra($concepto->bc, $num); ?></td>
									@endforeach
									<td></td>
								</tr>
							@endif
						</thead>
						<tbody>
							<?php 
							for ($i = 0; $i < count($data2); $i++) { ?>
								<tr>
									<td style="text-align: right;">{{ $data2[$i]->fecha }}</td>
									<td style="text-align: right;">{{ $reciboss[$i] }}</td>
									@if($data != '')
									<?php for ($a = 0; $a < count($data); $a++) {
										$encontrado = false;
										for ($e=0; $e < sizeof($montoss[$i]); $e++) { 
											if($montoss[$i][$e]->cid == $data[$a]->id) {
												echo '<td style="text-align: right;">' . number_format($montoss[$i][$e]->tot, 1, '.', ',') . '</td>';
												break;
											} 

											if($e == sizeof($montoss[$i]) - 1 && $encontrado == false) {
												echo '<td style="text-align: right;"></td>';
											}	
										}
									} 
									?>
									@endif
									<td style="text-align: right;">
										{{ number_format($data2[$i]->total, 1, '.', ',') }}
									</td>
								</tr>
							<?php } ?>
							@if(count($vts) != 0)
							<?php 
							
							for ($i=0; $i < count($vts); $i+=4) { 
								?>
								<tr>
									<td style="text-align: right;">{{ $vts[$i] }}</td>
									<td style="text-align: right;">{{ $vts[$i + 1] }}</td>
									@if($data != '')
									<?php for ($a = 0; $a < count($data); $a++) {
										$encontrado = false;
										$cadena_conceptos = explode('@', $vts[$i + 2]);
										$cont = 0;
										foreach ($cadena_conceptos as $row) { 
											$cont++;
											$cadena_concepto = explode(';', $row);	
											if($cadena_concepto[1] == $data[$a]->id) {
												echo '<td style="text-align: right;">' . number_format($cadena_concepto[0], 1, '.', ',') . '</td>';
												break;
											} 
											if($cont == count($cadena_conceptos) && $encontrado == false) {
												echo '<td style="text-align: right;"></td>';
											}
										}
									} 
									?>
									@endif
									<td style="text-align: right;">{{ number_format($vts[$i + 3], 1, '.', ',') }}</td>
								</tr>							
							<?php } ?>
							@endif
							<tr>
								<th style="text-align: right;">TOTAL</th>
								<th style="text-align: right;"></th>
								@if($data != '')
									@foreach($totaless as $total)
										<th style="text-align: right;">{{ number_format($total, 1, '.', ',') }}</th>
									@endforeach
								@endif
								<th style="text-align: right;">{{ $totaldetotales }}</th>
							</tr>
						</tbody>
					</table>
					<div>
						<p style="text-align: right;"><font><b>{{ $numeracions }}</b></font></p>
						<p style="text-align: right;"><font><b>TOTAL: {{ $totaldetotales }} ({{ $totaldetotaless }})</b></font></p>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>