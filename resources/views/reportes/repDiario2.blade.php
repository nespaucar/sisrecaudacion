<?php 
	if($data == '') {
		$celdas = 3;
	} else {
		$celdas = count($data) + 3;
	}
 ?>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>ReporteDiario-<?= $date;?></title>
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
					<td align="center" style="background-color:#E4FF00;">FECHA</td>
					<td align="center" style="background-color:#E4FF00;">N° RECIBO</td>
					@if($data != '')
						@foreach($data as $concepto)
							<td align="center" style="background-color:#E4FF00;">{{ $concepto->descripcion }}</td>
						@endforeach
					@endif
					<td align="center" style="background-color:#E4FF00;">TOTAL</td>
				</tr>
				@if($data != '')
					<tr>
						<td colspan="2"></td>
						<td align="center" style="background-color:#9CDF7A">CLAS. FINANCIERO</td>
						<td></td>
						@foreach($data as $concepto)
							<td align="center" style="color:#FF0000;">{{ $concepto->fc }}</td>
						@endforeach
						<td></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="center" style="background-color:#9CDF7A">CLAS. PRESUPUESTAL</td>
						<td></td>
						@foreach($data as $concepto)
							<td align="center" style="color:#FF0000;">{{ $concepto->bc }}</td>
						@endforeach
						<td></td>
					</tr>
				@endif
			</thead>
			<tbody>
				<?php 
				for ($i = 0; $i < count($data2); $i++) { ?>
					<tr>
						<td colspan="2"></td>
						<td align="left">{{ $data2[$i]->fecha }}</td>
						<td align="center">{{ $data2[$i]->numrecibo }}</td>
						@if($data != '')
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
						@endif
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
					<td align="center" style="background-color: #FFA04B;">TOTAL</td>
					<td style="background-color: #FFA04B;"></td>
					@if($data != '')
						@foreach($totaless as $total)
							<td align="right" style="background-color: #FFA04B;">{{ number_format($total->ds, 0, '', '') }}</td>
						@endforeach
					@endif
					<td align="right" style='background-color: #FFA04B;'>{{ $totaldetotales }}</td>
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
			<tr></tr>
				@if(count($papeletass) == 1)
					<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="left"><font>{{ $papeletass[0] }}</font></td></tr>
				@else
				<?php for ($i = 0; $i < count($papeletass); $i += 2) { ?>
				<tr>
					<td colspan="2"></td>
					<td colspan="1" align="right">
						V. {{ $papeletass[$i] }}
					</td>
					<td colspan="1" align="right">
						{{ number_format($papeletass[$i + 1], 0, '', '') }}
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
			@endif
		</table>
	</body>
</html>


