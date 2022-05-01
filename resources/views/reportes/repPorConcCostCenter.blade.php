@include('scripts.script_repGeneral')
<?php 
	$num = num($data, 40);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>ReporteDiario-<?= $date;?></title>
		
		<link rel="stylesheet" href="{{ asset('css/repDiario.css') }}">
		<style>
			body {
				font-size: 12px;
			}
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
				<div class="box-header with-border" style="text-align: left;">
					<font><b>FECHA: <?=  $date; ?></b></font>
				</div>
				<br>
				<div class="box-header" style="text-align: left;">
					<font><b>CENTRO DE COSTOS: <?= $n2;?></b></font>
				</div>
				<br>
				<div class="box-header" style="text-align: left;">
					<font><b>CONCEPTO: <?= $n1;?></b></font>
				</div>

				<div class="box-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<td style="width: 5%; text-align: center;">NUM</td>
								<td style="width: 8%; text-align: center;">FECHA</td>
								<td style="width: 40%;text-align: center;">CLIENTE</td>
								<td style="width: 8%;text-align: center;">N°REC.</td>
								<td style="width: 10%;text-align: center;">MONTO</td>
							</tr>
						</thead>
						<tbody>
							<?php $i=1; ?>
							@foreach($data2 as $fila)
								<tr>
									<td style="text-align: center;"><?php echo $i; $i++; ?></td>
									<td style="text-align: center;">{{ $fila->fecha }}</td>
									<td style="text-align: left;">&nbsp;&nbsp;&nbsp;{{ $fila->nombres }} {{ $fila->apellidop }} {{ $fila->apellidom }}</td>
									<td style="text-align: center;">{{$fila->cod }}</td>
									<td style="text-align: right;">{{ number_format($fila->totall, 2, '.', ',') }}&nbsp;&nbsp;&nbsp;</td>
								</tr>
							@endforeach
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td style="text-align: center;">TOTAL</td>
								<td style="text-align: right;">{{ $totaldetotales }}&nbsp;&nbsp;&nbsp;</td>
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


