<?PHP

	/*
		Autor: 	poodoo.cl
		Email:	contacto@poodoo.cl
	*/



	/*
		#	Consulta para sacar los datos de wp_post 

		INSERT INTO vestido (codigo,fecha_creacion,fecha_modificacion,imagen_url)
		SELECT 	P.post_title 	AS 'codigo',
				P.post_date 	AS 'fecha_creacion',
				P.post_date 	AS 'fecha_modificacion',
				(SELECT PO.guid 
				FROM wp_postmeta AS pm 
					INNER JOIN wp_posts AS PO 
						ON pm.meta_value=PO.ID  
				WHERE pm.post_id = P.ID 
					AND pm.meta_key = '_thumbnail_id'  
				ORDER BY PO.post_date DESC LIMIT 1) AS 'imagen_url'
		FROM wp_posts P
			INNER JOIN wp_term_relationships  R
		  		ON P.ID = R.object_id 
		WHERE P.post_status='publish' 
		  AND P.post_type='post' 
		  AND R.term_taxonomy_id = 6
	*/


	ini_set('memory_limit', '-1');
	//extension_loaded('zip');
	error_reporting(E_ALL);
	set_time_limit(0);

	date_default_timezone_set('America/Santiago');

	/** Include path **/
	set_include_path(get_include_path() . PATH_SEPARATOR . 'libraries/Classes/');
	require_once(dirname(__FILE__).'/libraries/class.upload.php');
	require_once(dirname(__FILE__).'/libraries/PHPExcel/IOFactory.php');
	require_once(dirname(__FILE__).'/libraries/Database.class.php');
	require_once(dirname(__FILE__).'/config.php');


	/*Pregunto si el formulario fue enviado */
	if(isset($_POST['upload_file'])) {
		try {
			$handle = new upload($_FILES['file_xls']);

		  	if ($handle->uploaded) {
		  		$uploadFileDir = dirname(__FILE__).'/uploads/';

		  		$path_parts = pathinfo($handle->file_src_name);

		  		$tempName = md5(time()."-".$handle->file_src_name);
		  		$handle->file_new_name_body = $tempName;

		      	$handle->process($uploadFileDir);


				$inputFileName = $handle->file_src_pathname;



				echo 'Cargando Archivo: ',pathinfo($handle->file_src_name,PATHINFO_BASENAME),' <br />';
				$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
				echo '<hr />';

				$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

				foreach ($sheetData as $key => $value) {
					/* Fila 1: Cabeceras del documento */
					if($key > 1) {
						/* Debe existir un código de vestido */
						if(!is_null($value["A"])) {
							$vestidoCodigo = strtoupper($value["A"]);

							$vestido = array(
								"precio_venta"			=> str_replace(array("$",".",","),array("","",""),$value["B"]),
								"descuento_venta"		=> str_replace(array("%",".",","),array("","",""),$value["C"]),
								"precio_arriendo"		=> str_replace(array("$",".",","),array("","",""),$value["D"]),
								"descuento_arriendo"	=> str_replace(array("%",".",","),array("","",""),$value["E"])
							);

							if(is_numeric($vestido["precio_venta"]) && (is_numeric($vestido["descuento_venta"]) && ($vestido["descuento_venta"] >= 0 && $vestido["descuento_venta"] <= 100)) && is_numeric($vestido["precio_arriendo"]) && (is_numeric($vestido["descuento_arriendo"]) && ($vestido["descuento_arriendo"] >= 0 && $vestido["descuento_arriendo"] <= 100))) {
								/* Actualiza Vestido */
								$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE); 
								$db->connect();

									$results = $db->fetch_all_array("SELECT * FROM vestido WHERE codigo='".$vestidoCodigo."'");
									if(count($results) > 0) {
										$isUpdated = $db->query_update("vestido", $vestido, "codigo='".$vestidoCodigo."'");
										if($isUpdated) {
											$db->query_insert("historial_precio", array(
												"codigo" 				=> $vestidoCodigo,
												"create_date"			=> date("Y-m-d H:i:s"),
												"precio_venta" 			=> $vestido["precio_venta"],
												"precio_arriendo"		=> $vestido["precio_arriendo"],
												"descuento_venta"		=> $vestido["descuento_venta"],
												"descuento_arriendo"	=> $vestido["descuento_arriendo"]
											));
											echo "<strong style='color: green'>OK</strong>: Actualizado vestido con código: ".$vestidoCodigo."<br />";
										} else {
											echo "<strong style='color: red'>ERROR</strong>: No se pudo actualizar el vestido con código: ".$vestidoCodigo." , revise los datos ingresados<br />";
										}
									} else {
										echo "<strong style='color: red'>ERROR</strong>: No existe el vestido con código: ".$vestidoCodigo."<br />";
									}
								$db->close();
							}
							//print_r($vestido);
							//echo "<br />";	
						}

					}
				}
				/*
				echo $handle->file_src_pathname;
				echo "<br />";
				echo $uploadFileDir.$tempName.".".$path_parts["extension"];
				*/
				@unlink($handle->file_src_pathname);
				@unlink($uploadFileDir.$tempName.".".$path_parts["extension"]);
		  	} else {
		  		print_r($handle->error);
		  	}
		} catch (Exception $e) {
			echo "<strong>ERROR</strong> al subir el archivo , ".$e->getMessage();

		}
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 //EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta charset="UTF-8" />
	</head>
	<body>
		<form method="post" enctype="multipart/form-data">
			<label for="file">Archivo .xls</label>
			<input type="file" name="file_xls" id="file_xls" />
			<input type="submit" name="upload_file" value="Upload" />
		</form>
	</body>
</html>