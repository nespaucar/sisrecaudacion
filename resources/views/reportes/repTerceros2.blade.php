<?php 
	$celdas = count($data) + 10;
 ?>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>ReporteTerceros-<?= $date;?></title>
	</head>
	<body>
		<table style="border-collapse:collapse;">
			<thead>
				<tr></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="center">UNIVERSIDAD NACIONAL PEDRO RUIZ GALLO</td></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="center">FACULTAD DE INGENIERIA CIVIL, SISTEMAS Y DE ARQUITECTURA - OFICINA DE ADMINISTRACION</td></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="center">INFORME DE RECURSOS DIRECTAMENTE RECAUDADOS - LAMBAYEQUE</td></tr>
				<tr></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="left"><font>Fecha: <?=  $date; ?></font></td></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="right"><font><?=  $numcuadros; ?></font></td></tr>
				<tr>
					<td colspan="2"></td>
					<td align="center">FECHA</td>
					<td style="text-align: center;">VOUCHER</td>
					<td style="text-align: center;">N°REC.</td>
					<td style="text-align: center;">DOCUMENTO</td>
					<td style="text-align: center;">H.RESUMEN</td>
					<td style="text-align: center;">RUC/DNI</td>
					<td style="text-align: center;">CLIENTE</td>
					@foreach($data as $concepto)
						<td align="center">{{ $concepto->descripcion }}</td>
					@endforeach
					<td style="text-align: center;">DETRACCIÓN</td>
					<td style="text-align: center;">IGV</td>
					<td align="center" style="background-color:#E4FF00;">TOTAL</td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td align="center" style="background-color:#9CDF7A">CLAS. FINANCIERO</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					@foreach($data as $concepto)
						<td align="center">{{ $concepto->fc }}</td>
					@endforeach
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td align="center" style="background-color:#9CDF7A">CLAS. PRESUPUESTAL</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					@foreach($data as $concepto)
						<td align="center">{{ $concepto->bc }}</td>
					@endforeach
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				<?php 
				for ($i = 0; $i < count($data2); $i++) { ?>
					<tr>
						<td colspan="2"></td>
						<td style="text-align: center;"">{{ $data2[$i]->fecha }}</td>
						<td style="text-align: left;"">{{ $data2[$i]->voucher }}</td>
						<td style="text-align: left;"">{{ $data2[$i]->numrecibo }}</td>
						<td style="text-align: left;"">{{ $data2[$i]->tipodoc }}</td>
						<td style="text-align: left;"">{{ $data2[$i]->numserie }}</td>
						<td style="text-align: left;"">{{ $data2[$i]->dni }}</td>
						<td style="text-align: left;"">{{ $data2[$i]->nombres }}</td>
						<?php for ($a = 0; $a < count($data); $a++) {
							$recorrido1 = 0; 
							$se_encuentra = false;
							$recorrido2 = 0;
							$total = 0.00;
							$montos = explode('+++', $montoss[$i]->cadena);
							for ($e = $recorrido2; $e < count($montos); $e++) {
								$se_encuentra = false;
								$monto = explode('@', $montos[$e]); 
								if($monto[2] == $data[$a]->id) {
									$total += $monto[1];
									$recorrido2 = $e;
									$se_encuentra = true;
								}
								if($e == count($montos) - 1) {
									if($total == 0){
										echo '<td></td>';
									} else {
										if($data2[$i]->anulado == 1){
											echo "<td align='right'>" . number_format($total, 0, '', '') . '</td>';
										} else {
											echo '<td>ANULADO</td>';
										}
									}
								}
							}
						} 
						?>
						<td style="text-align: right;"">{{ number_format($data2[$i]->detraccion, 2, '.', ',') }}</td>
						<td style="text-align: right;"">{{ number_format($data2[$i]->igv, 2, '.', ',') }}</td>
						<td align="right">
							@if($data2[$i]->anulado == 1) 
								{{ number_format($data2[$i]->monto, 0, '', '') }}
							@else
								0.00
							@endif
						</td>
					</tr>
				<?php } ?>
				<tr>
					<td colspan="2"></td>
					<td colspan="7" align="center" style="background-color: #FFA04B;">TOTAL</td>
					@foreach($totaless as $total)
						<td align="right">{{ number_format($total->ds, 0, '', '') }}</td>
					@endforeach
					@foreach($vts as $vt)
						<td style="text-align: right;">{{ number_format($vt->detraccion, 2, '.', ',') }}</td>
						<td style="text-align: right;">{{ number_format($vt->igv, 2, '.', ',') }}</td>
					@endforeach
					<td align="right">{{ $totaldetotales }}</td>
				</tr>
				<tr></tr>
				<tr>
					<td colspan="2"></td>
					@if(count($data2) != 1)
						<td colspan="{{ $celdas }}" align="right" style="color: #0008FF">
							{{ $numeracions }}
						</td>
					@endif
				</tr>
				<tr>
					<td colspan="2"></td>
					<td colspan="{{ $celdas }}" align="right" style="color: #0008FF">
						TOTAL: {{ $totaldetotales }} ({{ $totaldetotaless }})
					</td>
				</tr>
			</tbody>
		</table>
		<table>
			<?php for ($i = 0; $i < count($data2); $i++) { ?>
			<tr>
				<td colspan="2"></td>
				<td colspan="1" align="right">
					V. {{ $data2[$i]->voucher }}
				</td>
				<td colspan="1" align="right">
					{{ number_format($data2[$i]->monto, 2, '.', ',') }}
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td colspan="2"></td>
				<td colspan="1" align="right">
					Total
				</td>
				<td colspan="1" align="right">
					{{ $totaldetotales }}
				</td>
			</tr>
		</table>
	</body>
</html>


