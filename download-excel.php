<?PHP
	ini_set('memory_limit', '-1');
	error_reporting(E_ALL);
	set_time_limit(0);



	$imagen = (isset($_GET["include_image"]) ? $_GET["include_image"] : false);
	/** Include path **/
	set_include_path(get_include_path() . PATH_SEPARATOR . 'libraries/Classes/');
	require_once(dirname(__FILE__).'/libraries/class.upload.php');
	require_once(dirname(__FILE__).'/libraries/PHPExcel/IOFactory.php');
	require_once(dirname(__FILE__).'/libraries/Database.class.php');
	require_once(dirname(__FILE__).'/config.php');



	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("poodoo software")
							 ->setLastModifiedBy("Esteban Fuentealba")
							 ->setTitle("Precios deblanco.cl")
							 ->setSubject("Precios deblanco.cl")
							 ->setDescription("Listado de precio y descuentos de vestidos del sitio deblanco.")
							 ->setKeywords("office php")
							 ->setCategory("catalogo");

	$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE); 
	$db->connect();

		$results = $db->fetch_all_array("SELECT * FROM vestido");
		if(count($results) > 0) {
			$rowIndex = 2;

			// Agrego cabeceras de la tabla
			$objPHPExcel->setActiveSheetIndex(0)
	            ->setCellValue('A1', 'Código Vestido')
	            ->setCellValue('B1', 'Precio Venta')
	            ->setCellValue('C1', 'Descuento Venta')
	            ->setCellValue('D1', 'Precio Arriendo')
	            ->setCellValue('E1', 'Descuento Arriendo');

	        if($imagen) {
	        	$objPHPExcel->getActiveSheet()
	        				->setCellValue('F1', 'Imagen Vestido');
	        }
	        

	        // doy auto size a las columnas
	        $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
			//$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);


			// Estilo de la cabecera de la tabla

	        if($imagen) {
		        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray(array(
		        	'fill' => array(
				        'type' => PHPExcel_Style_Fill::FILL_SOLID,
				        'color' => array('rgb'=>'2FB5DB')
				    ),
				    'font' => array(
				        'bold' => true,
				        'color' => array('rgb'=>'FFFFFF')
				    )
		        ));
		    } else {
		    	$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray(array(
		        	'fill' => array(
				        'type' => PHPExcel_Style_Fill::FILL_SOLID,
				        'color' => array('rgb'=>'2FB5DB')
				    ),
				    'font' => array(
				        'bold' => true,
				        'color' => array('rgb'=>'FFFFFF')
				    )
		        ));
		    }


		    // doy formato a las celas con porcentaje
	        $objPHPExcel->getActiveSheet()
	        			->getStyle('C')
	        			->getNumberFormat()
	        			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

	        $objPHPExcel->getActiveSheet()
	        			->getStyle('E')
	        			->getNumberFormat()
	        			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

			foreach($results as $index => $row) {
				
				if($imagen) {
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setPath(str_replace("http://www.deblanco.cl/","/home/deblanco/public_html/",$row["imagen_url"]));
					$objDrawing->setCoordinates('F'.$rowIndex);
					$objDrawing->setHeight(100);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
				}
				$objPHPExcel->setActiveSheetIndex(0)
				            ->setCellValue('A'.$rowIndex, $row["codigo"])
				            ->setCellValue('B'.$rowIndex, $row["precio_venta"])
				            ->setCellValue('C'.$rowIndex, ($row["descuento_venta"] / 100))
				            ->setCellValue('D'.$rowIndex, $row["precio_arriendo"])
				            ->setCellValue('E'.$rowIndex, ($row["descuento_arriendo"] / 100));
				if($imagen) {
					$objPHPExcel->getActiveSheet()
								->getRowDimension($rowIndex)
								->setRowHeight(90);


					$objPHPExcel->getActiveSheet()
								->getStyle('A'.$rowIndex.':F'.$rowIndex)
								->getAlignment()
								->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				}
				
				$rowIndex++;
			}

			$db->close();


			


			$objPHPExcel->getActiveSheet()->setTitle('Precios de Catálogo');
			$objPHPExcel->setActiveSheetIndex(0);

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="deblanco_precios-'.date('d-m-Y').'.xls"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
?>