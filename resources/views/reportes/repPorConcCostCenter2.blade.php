<?php 
	if($data == '') {
		$celdas = 5;
	} else {
		$celdas = count($data) + 5;
	}
?>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>ReportePorConcCentrCost-<?= $date;?></title>
		
	</head>
	<body>
		<table>
			<thead>
				<tr></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="center">UNIVERSIDAD NACIONAL PEDRO RUIZ GALLO</td></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="center">FACULTAD DE INGENIERIA CIVIL, SISTEMAS Y DE ARQUITECTURA - OFICINA DE ADMINISTRACION</td></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="center">INFORME DE RECURSOS DIRECTAMENTE RECAUDADOS - LAMBAYEQUE</td></tr>
				<tr></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="left"><font>Fecha: <?=$date; ?></font></td></tr>
				<tr></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="left"><font>Centro de Costo: <?=$nn2; ?></font></td></tr>
				<tr><td colspan="2"></td><td colspan="{{ $celdas }}" align="left"><font>Concepto: <?=$nn1; ?></font></td></tr>
				<tr></tr>
				<tr>
					<td colspan="2"></td>
					<td align="center" style="background-color:#E4FF00;">NUM</td>
					<td align="center" style="background-color:#E4FF00;">FECHA</td>
					<td align="center" style="background-color:#E4FF00;">CLIENTE</td>
					<td align="center" style="background-color:#E4FF00;">N°REC</td>
					<td align="center" style="background-color:#E4FF00;">MONTO</td>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1; ?>
				@foreach($data2 as $detalle)
				<tr>
					<td colspan="2"></td>
					<td align="center" style="background-color:#E4FF00;">{{ $i }}</td>
					<?php $i++; ?>
					<td align="center">{{ $detalle->fecha }}</td>
					<td align="left">
						{{ $detalle->nombres }} {{ $detalle->apellidop }} {{ $detalle->apellidom }}
					</td>
					<td align="center">{{ $detalle->cod }}</td>
					<td align="center">{{ $detalle->totall }}</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2"></td>
					<td colspan="3"></td>
					<td align="center" style="background-color: #FFA04B;">TOTAL</td>
					<td align="center">{{ $totaldetotales }}</td>
				<tr></tr>
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