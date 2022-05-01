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
		<title>ReporteMensual-<?= $date;?></title>
		
	</head>
	<body>
		<table>
			<thead>
				<tr></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="center">UNIVERSIDAD NACIONAL PEDRO RUIZ GALLO</td></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="center">FACULTAD DE INGENIERIA CIVIL, SISTEMAS Y DE ARQUITECTURA - OFICINA DE ADMINISTRACION</td></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="center">INFORME MENSUAL DE RECURSOS DIRECTAMENTE RECAUDADOS - LAMBAYEQUE</td></tr>
				<tr></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="left"><font>Fecha: <?=  $date; ?></font></td></tr>
				<tr></tr>
				<tr>
					<td colspan="2"></td>
					<td align="center" style="background-color:#E4FF00;">FECHA</td>
					<td align="center" style="background-color:#E4FF00;">N° RECIBO</td>
					@if($data != '')
						@foreach($data as $concepto)
							<td lign="center" style="background-color:#E4FF00;">{{ $concepto->descripcion }}</td>
						@endforeach
					@endif
					<td align="center" style="background-color:#E4FF00;">TOTAL</td>
				</tr>
				@if($data != '')
					<tr>
						<td colspan="2"></td>
						<td align="center" style="background-color:#9CDF7A">CLAS. FINANCIERO</td>
						<td colspan="1"></td>
						@foreach($data as $concepto)
							<td align="center" style="color:#FF0000;">{{ $concepto->fc }}</td>
						@endforeach
						<td colspan="1"></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="center" style="background-color:#9CDF7A">CLAS. PRESUPUESTAL</td>
						<td colspan="1"></td>
						@foreach($data as $concepto)
							<td align="center" style="color:#FF0000;">{{ $concepto->bc }}</td>
						@endforeach
						<td colspan="1"></td>
					</tr>
				@endif
			</thead>
			<tbody>
				<?php 
				for ($i = 0; $i < count($data2); $i++) { ?>
					<tr>
						<td colspan="2"></td>
						<td align="left">{{ $data2[$i]->fecha }}</td>
						<td align="center">{{ $reciboss[$i] }}</td>
						@if($data != '')
						<?php for ($a = 0; $a < count($data); $a++) {
							$encontrado = false;
							for ($e=0; $e < sizeof($montoss[$i]); $e++) { 
								if($montoss[$i][$e]->cid == $data[$a]->id) {
									echo '<td style="text-align: right;">' . number_format($montoss[$i][$e]->tot, 2, '.', ',') . '</td>';
									break;
								} 

								if($e == sizeof($montoss[$i]) - 1 && $encontrado == false) {
									echo '<td style="text-align: right;"></td>';
								}	
							}
						} 
						?>
						@endif
						<td align="right">
							{{ number_format($data2[$i]->total, 2, '.', ',') }}
						</td>
					</tr>
				<?php } ?>
				@if(count($vts) != 0)
				<?php 
				
				for ($i=0; $i < count($vts); $i+=4) { 
					?>
					<tr>
						<td colspan="2"></td>
						<td align="left">{{ $vts[$i] }}</td>
						<td align="center">{{ $vts[$i + 1] }}</td>
						@if($data != '')
						<?php for ($a = 0; $a < count($data); $a++) {
							$encontrado = false;
							$cadena_conceptos = explode('@', $vts[$i + 2]);
							$cont = 0;
							foreach ($cadena_conceptos as $row) { 
								$cont++;
								$cadena_concepto = explode(';', $row);	
								if($cadena_concepto[1] == $data[$a]->id) {
									echo '<td align="right">' . number_format($cadena_concepto[0], 1, '.', ',') . '</td>';
									break;
								} 
								if($cont == count($cadena_conceptos) && $encontrado == false) {
									echo '<td align="right"></td>';
								}
							}
						} 
						?>
						@endif
						<td  align="right">{{ number_format($vts[$i + 3], 1, '.', ',') }}</td>
					</tr>							
				<?php } ?>
				@endif
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2"></td>
					<td align="center" style="background-color: #FFA04B;">TOTAL</td>
					<td style="background-color: #FFA04B;"></td>
					@if($data != '')
						@foreach($totaless as $total)
							<td align="right" style="background-color: #FFA04B;">{{ number_format($total, 2, '.', ',') }}</td>
						@endforeach
					@endif
					<td align="right" style="background-color: #FFA04B;">{{ $totaldetotales }}</td>
				</tr>
				<tr></tr>
				<tr>
					<td colspan="2"></td>
					<td colspan="{{ $celdas }}" align="right" style="color: #0008FF">
						{{ $numeracions }}
					</td>
				</tr>
				<tr>
					<td colspan="2"></td>
					<td colspan="{{ $celdas }}" align="right" style="color: #0008FF">
						TOTAL: {{ $totaldetotales }} ({{ $totaldetotaless }})
					</td>
				</tr>
			</tfoot>
		</table>
	</body>
</html>


