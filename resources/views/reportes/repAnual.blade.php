@include('scripts.script_repGeneral')
<?php 
	$num = num($data, 40);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>ReporteAnual-<?= $date;?></title>
		
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
					<font>CONSOLIDADO ANUAL DE INGRESOS</font><br>
				</div>
				<br>
				<div class="box-header with-border" style="text-align: left;">
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
										<td style="text-align: center;"><?php echo ordenarPalabra($concepto->descripcion,$num); ?></td>
									@endforeach
								@endif
								<td style="text-align: center;">TOTAL</td>
							</tr>
							@if($data != '')
								@if(count($data) <= 8) 
								<tr>									
									<td style="text-align: center;">C.FIN</td>
									<td></td>
									@foreach($data as $concepto)
										<td style="text-align: center;"><?php echo ordenarPalabra($concepto->fc, $num); ?></td>
									@endforeach
									<td></td>
								</tr>
								@endif
								<tr>
									<td style="text-align: center;">C.PR</td>
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
									<td style="text-align: right;">{{ $data2[$i]->fech }}</td>
									<td style="text-align: right;">{{ $data2[$i]->numserie }}</td>
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
							<tr>
								<th style="text-align: right;">TOTAL</th>
								<th style="text-align: right;"></th>
								@if($data != '')
									@foreach($totaless as $total)
										<th style="text-align: right;">{{ number_format($total->ds, 1, '.', ',') }}</th>
									@endforeach
								@endif
								<th style="text-align: right;">{{ $totaldetotales }}</th>
							</tr>
						</tbody>
					</table>
					<div>
						<p style="text-align: right;"><font><b>TOTAL: {{ $totaldetotales }} ({{ $totaldetotaless }})</b></font></p>
					</div>
					
				</div>
			</div>
		</div>
	</body>
</html>


