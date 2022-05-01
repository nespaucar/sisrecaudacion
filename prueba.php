
<?php 
	$handle = printer_open("EPSON L375 Series");   

	if($handle) {
		//ABRE LA IMPRESORA
		printer_start_doc($handle, "Mi Documento");
		printer_start_page($handle);

		//LAS FUENTES (TIPO LETRA, ALTO, ANCHO, NEGRITA O NORMAL, CURSIVO, SUBRAYADO Y ORIENTACION)
		$font = printer_create_font("Sans Serif", 100, 60, 60, false, false, false, 0);
		printer_select_font($handle, $font);

		//ESCRIBIR RECIÉN (LINEA, LATITUD, LONGITUD)
		printer_draw_text($handle,"                MESA: 1", 50, 0);
		printer_draw_text($handle,"    ----ESTADO DE CUENTA----",50,200);
		$font = printer_create_font("Sans Serif", 80, 50, 50, false, false, false, 0);
		printer_select_font($handle, $font);
		printer_draw_text($handle, "RESTAURANT: PALADARES S.A.", 50, 350);
		printer_draw_text($handle, "RUC: 166264648", 50, 500);
		printer_draw_text($handle, "DIRECCION: Balta #125", 50, 650);
		printer_draw_text($handle, "FECHA: " . date("Y-n-j") . "  HORA: ". date("H:i"), 50, 800);
		printer_draw_text($handle, "MESERO: RONAL CAMPECHANO. ", 00, 150);
		printer_draw_text($handle," CANT.    DESCRIP.        SUBTOT.", 100, 1100);

		//TERMINACIÓN DEL DOCUMENTO
		printer_delete_font($font);
		printer_end_page($handle);
		printer_end_doc($handle);
		printer_close($handle);
	} else {
		echo 'LA IMPRESORA ESTÁ DESCONECTADA';
	}   
 ?>
