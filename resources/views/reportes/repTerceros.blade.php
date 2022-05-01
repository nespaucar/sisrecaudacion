@include('scripts.script_repGeneral')
<?php 
	$num = num($data, 40);
?>
<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>ReporteTerceros-<?= $date;?></title>
		
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
				</div>
				<br>
				<div class="box-header">
					<div style="text-align: left;"><font><b>Fecha: <?=  $date; ?></b></font></div>
					<div style="text-align: right;"><font><b><?= $numcuadros; ?></b></font></div>
				</div>

				<div class="box-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<td style="width: 55px; text-align: center;">FECHA</td>
								<td rowspan="3" style="width: 55px; text-align: center;">VOUCHER</td>
								<td rowspan="3" style="width: 55px; text-align: center;">N°REC.</td>
								<td rowspan="3" style="width: 55px; text-align: center;">DOCUMENTO</td>
								<td rowspan="3" style="width: 55px; text-align: center;">H.RESUMEN</td>
								<td rowspan="3" style="width: 60px; text-align: center;">RUC/DNI</td>
								<td rowspan="3" style="width: 200px; text-align: center;">CLIENTE</td>
								@foreach($data as $concepto)
									<td style="text-align:center;"><?php echo ordenarPalabra($concepto->descripcion,$num); ?></td>
								@endforeach
								<td rowspan="3" style="width: 55px;text-align: center;">DETRACCIÓN</td>
								<td rowspan="3" style="width: 55px;text-align: center;">IGV</td>
								<td rowspan="3" style="width: 55px;text-align: center;">TOTAL</td>
							</tr>
							<tr>									
								<td style="text-align: center;">C.FIN</td>
								@foreach($data as $concepto)
									<td style="text-align: center;"><?php echo ordenarPalabra($concepto->fc, $num); ?></td>
								@endforeach
							</tr>
							<tr>
								<td style="text-align: center;">C.PR</td>
								@foreach($data as $concepto)
									<td style="text-align: center;"><?php echo ordenarPalabra($concepto->bc, $num); ?></td>
								@endforeach
							</tr>
						</thead>
						<tbody>
							<?php 
							for ($i = 0; $i < count($data2); $i++) { ?>
								<tr>
									<td style="text-align: center;"">{{ $data2[$i]->fecha }}</td>
									<td style="text-align: left;"">{{ $data2[$i]->voucher }}</td>
									<td style="text-align: left;"">{{ $data2[$i]->numrecibo }}</td>
									<td style="text-align: left;"">{{ $data2[$i]->tipodoc }}</td>
									<td style="text-align: left;"">{{ $data2[$i]->numserie }}</td>
									<td style="text-align: left;"">{{ $data2[$i]->dni }}</td>
									<td style="text-align: left;"">{{ $data2[$i]->nombres }}</td>
									<?php for ($a = 0; $a < count($data); $a++) {
										$recorrido2 = 0;
										$total = 0.00;
										$montos = explode('+++', $montoss[$i]->cadena);
										for ($e = $recorrido2; $e < count($montos); $e++) {
											$monto = explode('@', $montos[$e]); 

											if($monto[2] == $data[$a]->id) {
												$total += $monto[1];
												$recorrido2 = $e;
											}
											if($e == count($montos) - 1) {
												if($total == 0){
													echo '<td style="text-align: right;"></td>';
												} else {
													if($data2[$i]->anulado == 1){
														echo '<td style="text-align: right;">'.number_format($total, 2, '.', ',') . '</td>';
													} else {
														echo '<td style="text-align: right;">AN.</td>';
													}
												}
											}
										}
									} 
									?>
									<td style="text-align: right;"">{{ number_format($data2[$i]->detraccion, 2, '.', ',') }}</td>
									<td style="text-align: right;"">{{ number_format($data2[$i]->igv, 2, '.', ',') }}</td>
									<td style="text-align: right;">
										@if($data2[$i]->anulado == 1) 
											{{ number_format($data2[$i]->monto, 2, '.', ',') }}
										@else
											0.00
										@endif
									</td>
								</tr>
							<?php } ?>
							<tr>
								<th colspan="7" style="text-align: center;">TOTAL</th>
								@foreach($totaless as $total)
									<th style="text-align: right;">{{ number_format($total->ds, 2, '.', ',') }}</th>
								@endforeach
								@foreach($vts as $vt)
									<th style="text-align: right;">{{ number_format($vt->detraccion, 2, '.', ',') }}</th>
									<th style="text-align: right;">{{ number_format($vt->igv, 2, '.', ',') }}</th>
								@endforeach
								<th style="text-align: right;">{{ $totaldetotales }}</th>
							</tr>
						</tbody>
					</table>
					<div>
						@if(count($data2) != 1)
							<p style="text-align: right;"><font><b>{{ $numeracions }}</b></font></p>
						@endif
						<p style="text-align: right;"><font><b>TOTAL: {{ $totaldetotales }} ({{ $totaldetotaless }})</b></font></p>
					</div>
					<div>
						<table>
							<?php for ($i = 0; $i < count($data2); $i++) { ?>
							<tr>
								<th width="20%" style="text-align: right">
									<b>V. {{ $data2[$i]->voucher }}</b>
								</th>
								<td width="20%" style="text-align: right">
									{{ number_format($data2[$i]->monto, 2, '.', ',') }}
								</td>
								<td width="80%"></td>
							</tr>
							<?php } ?>
							<tr>
								<th width="20%" style="text-align: right">
									<b>Total</b>
								</th>
								<th width="20%" style="text-align: right">
									{{ $totaldetotales }}
								</th>
								<td width="80%"></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>