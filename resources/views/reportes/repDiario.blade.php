@include('scripts.script_repGeneral')
<?php 
	$num = num($data, 40);
	$titulo = '<div class="box-header" style="text-align: center;"><font>UNIVERSIDAD NACIONAL PEDRO RUIZ GALLO</font><br><font>FACULTAD DE INGENIERIA CIVIL, SISTEMAS Y ARQUITECTURA - OFICINA DE ADMINISTRACION</font><br><font>INFORME DE RECURSOS DIRECTAMENTE RECAUDADOS - LAMBAYEQUE</font><br></div><br><div class="box-header"><div style="text-align: left;"><font><b>Fecha: ' .  $date . '</b></font></div><div style="text-align: right;"><font><b>' . $numcuadros . '</b></font></div></div>';

	$cabeza = '<thead><tr><td'; 
	if($data != '') {
		$cabeza .= ' style="width: 55px; text-align: center;"';
	}
	else {
		$cabeza .= ' style="width: 33%; text-align: center;"';	
	}
	$cabeza .= '>FECHA</td><td'; 
	if($data != '') {
		$cabeza .= ' style="width: 55px; text-align: center;"';
	} else{
		$cabeza .= ' style="width: 33%; text-align: center;"';
	}
	$cabeza .= '>N°REC.</td>';
	if($data != ''){
		foreach($data as $concepto) {
			$cabeza .= '<td style="text-align:center;">' . ordenarPalabra($concepto->descripcion,$num) . '</td>';
		}
	}
	$cabeza .= '<td style="text-align: center;">TOTAL</td></tr>';
	if($data != '') {
		$cabeza .= '<tr><td style="text-align: center;">C.FIN</td><td></td>';
		foreach($data as $concepto){
			$cabeza .= '<td style="text-align: center;">' . ordenarPalabra($concepto->fc, $num) . '</td>';
		}
		$cabeza .= '<td></td></tr><tr><td style="text-align: center;">C.PR</td><td></td>';
		foreach($data as $concepto) {
			$cabeza .= '<td style="text-align: center;">' . ordenarPalabra($concepto->bc, $num) . '</td>';
		}
		$cabeza .= '<td></td></tr>';
	}
	$cabeza .= '</thead>';

	$array = crearArray($data2, $montoss);

	$saltopag = cant_tope($array, 36);
	if($data == '') {
		$saltopag = cant_tope($array, 53);
	}	

	$numpags = round_up(count($array)/$saltopag, 0);
	$tope = $saltopag;
	if($numpags == 1){
		$tope = count($data2);
	}	

	$van = '';
	$nombre = '';
	$cadenamontos = '';	

	$inic = 0;
?>
<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>ReporteDiario-<?= $date;?></title>
		
		<link rel="stylesheet" href="{{ asset('css/repDiario.css') }}">
		<script src="{{ asset('js/jquery-2.1.3.min.js') }}"></script>

		@if($data == '')
			<style>
				body {
					font-size: 12px;
				}
			 </style>
		@endif
		<style>
		    #footer { position: absolute; left: 0px; top: 85%; right: 0px; }
		    #content {
			    width: 100%;
			    margin-top: 0px;
			    margin-bottom: 120px;
			}
		 </style>
	</head>
	<body>
		<div id="content" class="col-md-12">
			<div class="box">
				<div id="tablita">
					<div class="box-body">
						<?php 
						for ($ii = 0; $ii < $numpags; $ii++) { 
						echo $titulo; ?> 
						<table class="table table-bordered">
							
							<?php echo $cabeza; ?> 
							<tbody>
							@if($data != '')
								@if($van != '')
								<tr>
									<th></th><th>VIENEN</th>
								@endif
								<?php 
									echo $van;
									for ($i = ($ii * $saltopag); $i < $tope; $i++) { ?>
									<tr>
										<td style="text-align: right;">{{ $data2[$i]->fecha }}</td>
									<?php
										$subt = 0;
										$idconc = '';
										if($array[$inic] == 1) {
											$nombre = $data2[$i]->numrecibo;
											$cadenamontos = $montoss[$i]->cadena;															
										} else {
											$cadenamontos = '';
											$nombre = $data2[$i]->numrecibo . '/' . $data2[$i + $array[$inic] - 1]->numrecibo;
											if ($data2[$i]->anulado != 1){
												$cadenamontos = $montoss[$i]->cadena;
											} else {
												for ($w = 0; $w < $array[$inic]; $w++) { 
													$monto = explode('@', $montoss[$i + $w]->cadena);
													$subt += $monto[1];
													$idconc = $monto[2];
												}
												$cadenamontos = '0' . '@' . $subt . '@' . $idconc;
											}
										}																			

									 ?><td style="text-align: right; font-size: 7px">R-{{ $nombre }}</td>
										<?php
										$total2 = 0.00;
										for ($a = 0; $a < count($data); $a++) {
											$recorrido2 = 0;
											$total = 0.00;
											$montos = explode('+++', $cadenamontos);
											for ($e = $recorrido2; $e < count($montos); $e++) {
												$monto = explode('@', $montos[$e]); 
												if($monto[2] == $data[$a]->id) {
													$total += $monto[1];
													$total2 += $monto[1];
													$recorrido2 = $e;
												}
												if($e == count($montos) - 1) {
													if($total == 0){
														echo '<td style="text-align: right;"></td>';
													} else {
														if($data2[$i]->anulado == 1) {
															echo '<td style="text-align: right;">'.number_format($total, 2, '.', ',') . '</td>';
														} else {
															echo '<td style="text-align: right;">AN.</td>';
														}
													}
												}
											}
										}?>
										<td style="text-align: right;">
											@if($data2[$i]->anulado == 1) 
												{{ number_format($total2, 2, '.', ',') }}
											@else
												0.00
											@endif
										</td>
									<?php 
										$i += ($array[$inic] - 1);
										$inic++;
									}	?>
								@else
								<?php
									for ($i=0; $i < count($data2); $i++) { 
										echo '<tr><td style="text-align: right;">' . $data2[$i]->fecha . '</td>';

										echo '<td style="text-align: right;">' . $data2[$i]->numrecibo . '</td>';

										if($data2[$i]->anulado == false){
											echo '<td style="text-align: right;"> 0.00 </td>';
										} else {
											echo '<td style="text-align: right;">' . number_format($data2[$i]->monto, 2, '.', ',') . '</td>';
										}
									}
								?>
								@endif
								</tr>
								@if($ii == $numpags - 1)
								<tr>
									<th style="text-align: right;">TOTAL</th>
									<th style="text-align: right;"></th>
									@if($data != '')
										@foreach($totaless as $total)
											<th style="text-align: right;">{{ number_format($total->ds, 2, '.', ',') }}</th>
										@endforeach
									@endif
									<th style="text-align: right;">{{ $totaldetotales }}</th>
								</tr>
								@else
									<tr>
										<th></th><th>VAN</th>
									<?php 
									$van = '';
									for ($i=0; $i < count($data); $i++) { 
										$van .= '<th style="text-align: right;">' . subtotal($data[$i]->id, $data2, $montoss, ($ii * $saltopag), $tope, 1) . '</th>';
									}
									$van .= '<th style="text-align: right;">' . subtotal(0, $data2, $montoss, ($ii * $saltopag), $tope, 2) . '</th>';
									$van .= '</tr>';	 
									echo $van;
									?>
								@endif
							</tbody>
						</table>
						<?php 
							if($ii == $numpags - 2){
								$tope = count($data2);		
								echo '<div style="page-break-after:always;"></div>';				
							} else {
								$tope += $saltopag;	
								if($ii != $numpags - 1){	
									echo '<div style="page-break-after:always;"></div>';
								}
							}						
						}
						?>
						
						<div>
							@if(count($data2) != 1)
								<p style="text-align: right;"><font><b>{{ $numeracions }}</b></font></p>
							@endif
							<p style="text-align: right;"><font><b>TOTAL: {{ $totaldetotales }} ({{ $totaldetotaless }})</b></font></p>
							<table>
								@if(count($papeletass) == 1)
									<tr>
										<th style="text-align: left">{{ $papeletass[0] }}</th>
									</tr>
								@else
									<?php for ($i = 0; $i < count($papeletass); $i += 2) { ?>
									<tr>
										<th width="20%" style="text-align: right">
											<font><b>V. {{ $papeletass[$i] }}</b></font>
										</th>
										<td width="20%" style="text-align: right">
											<font>{{ number_format($papeletass[$i + 1], 2, '.', ',') }}</font>
										</td>
										<td width="80%"></td>
									</tr>
									<?php } ?>
									<tr>
										<th width="20%" style="text-align: right">
											<font><b>Total</b></font>
										</th>
										<th width="20%" style="text-align: right">
											<font>{{ $totaldetotales }}</font>
										</th>
										<td width="80%"></td>
									</tr>
								@endif
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="footer">
			<div class="fdizquierda">
				<div class="footerdivs">
					<font class="pals" align="center">___________________________<br>Ernesto Raúl Ríos Damián<br> <b>Recaudador</b></font>
				</div>
			</div>
			
			<div class="fdderecha">
				<div class="footerdivs">
					<font class="pals" align="center">____________________________________<br>Ing. Luis Alberto Llontop Cumpa<br> <b>Jefe de Oficina de Administración</b></font>
				</div>
			</div>

			<div class="fdcentro">
				<div class="footerdivs">
					<font class="pals" align="center">_______________________________________<br>CPC. Bertha Elizabeth Delgado Posadas<br> <b>Contadora</b></font>
				</div>
			</div>
		</div>
	</body>
</html>