<?php 
	
	function num($data, $num) {
		if($data != '') {
			if(count($data) <= 4) {
				$num = 40;
			} else if(count($data) >= 5 && count($data) <= 10) {
				$num = 9;
			} else if(count($data) >= 11 && count($data) <= 22) {
				$num = 5;
			} else if(count($data) >= 23 && count($data) <= 28) {
				$num = 4;
			} else if(count($data) >= 29 && count($data) <= 35) {
				$num = 3;
			} else if(count($data) >= 36 && count($data) <= 40) {
				$num = 2;
			} else {
				$num = 1;
			}
		}
		return $num;
	}	

	function ordenarPalabra($cadena, $num) {
		$separaciones = round(strlen($cadena)/$num, 0, PHP_ROUND_HALF_UP);
		$recorrido = 0;
		$cadenadevuelta = '';
		for ($i=0; $i < $separaciones; $i++) { 
			$cadenadevuelta .= substr($cadena, $recorrido, $num) . '<br>';
			$recorrido += $num;
		}
		if(strlen($cadena) != strlen($cadenadevuelta)) {
			$cadenadevuelta .= substr($cadena, $recorrido, $num);
		} 
		return $cadenadevuelta;
	}

	function subtotal($idconcepto, $data2, $montoss, $detalle_inicio, $detalle_final, $tipo){
		$subtotal = 0;
		$subtotal2 = 0;
		for ($i = 0; $i < $detalle_final; $i++) { 
			if($data2[$i]->anulado == 1) {
				$montos = explode('+++', $montoss[$i]->cadena);
				for ($e = 0; $e < count($montos); $e++) { 
					$monto = explode('@', $montos[$e]);
					if($idconcepto != 0){
						if($monto[2] == $idconcepto) {
							$subtotal += $monto[1];
						}
					} else {
						$subtotal2 += $monto[1];
					}
				}
			}
		}
		if($tipo == 1) {
			return number_format($subtotal, 2, '.', ',');
		} else {
			return number_format($subtotal2, 2, '.', ',');
		}
	}

	function crearArray($data2, $montoss) {
		$cant = 1;
		for ($i = 0; $i < count($montoss); $i++) { 
			if($i != count($montoss) - 1) {
				if($data2[$i]->anulado != $data2[$i + 1]->anulado) {
					$array[] = 1;
				} else {
					$cant_detalles = cant_detalles($data2, $montoss, $i);
					$array[] = $cant_detalles;
					$i += $cant_detalles - 1;
				}
			} else {
				$array[] = 1;
			}
		}
		return $array;
	}

	function cant_detalles($data2, $montoss, $i) {
		$cantidad = 1;
		$montos1 = explode('+++', $montoss[$i]->cadena);
		$montos2 = explode('+++', $montoss[$i + 1]->cadena);
		if(count($montos1) > 1) {
			return 1;
		} else {
			if(count($montos2) > 1) {
				return 1;
			} else {
				for ($b = $i; $b < count($data2) - 1; $b++) { 
					$montos1 = explode('+++', $montoss[$b]->cadena);
					$montos2 = explode('+++', $montoss[$b + 1]->cadena);
					$monto1 = explode('@', $montos1[0]);
					$monto2 = explode('@', $montos2[0]);
					if(count($montos2) == 1) {					
						if($b != count($data2) - 1) {
							if($data2[$b]->anulado == $data2[$b + 1]->anulado) {
								if($monto1[2] == $monto2[2]) {
									$cantidad ++;
								} else {
									break;
								}							
							} else {
								break;
							}
						} 	
					} 				
				}
				return $cantidad;
			}
		}
	}

	function round_up($value, $places) {
	    $mult = pow(10, abs($places)); 
	    return $places < 0 ?
	    ceil($value / $mult) * $mult :
	    ceil($value * $mult) / $mult;
	}

	function cant_tope($array, $num) {
		$retorno = 0;
		if(count($array) > $num) {
			for ($i = 0; $i < $num; $i++) { 
				$retorno += $array[$i];
			}
			return $retorno;
		} else {
			return $num;
		}			
	}
?>	