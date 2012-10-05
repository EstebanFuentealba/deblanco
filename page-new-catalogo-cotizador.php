<?php session_start();
function Conectarse()
{
$db_host="localhost";
$db_nombre="deblanco_wp";
$db_user="deblanco";
$db_pass="novias2011";
$link=mysql_connect($db_host, $db_user, $db_pass) or die ("Error conectando a la base de datos.");
mysql_select_db($db_nombre ,$link) or die("Error seleccionando la base de datos."); 
return $link;
}
$idCotizacion = null;
echo $_POST['talla1'];
// codigos
$codigos = array();
$codigos = $_SESSION['codigos'];

if ( isset ($_POST['codigo']) && !isset($_POST['refresh']) )
{
		$codigos[count($codigos)] = $_POST['codigo'];
}

$_SESSION['codigos'] = $codigos;
	/* 
		Template Name: Catalogo - cotizador
	*/
if (count($_SESSION['codigos']) == 0) { header ("Location: http://www.deblanco.cl/catalogo/"); }
?>
<?php get_header(); ?>

<?php get_template_part('includes/breadcrumbs','page'); ?>
<script>
	$(function() {
		$( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
	});
</script>
<div id="content-border" class="fullwidth">
	<div id="content-top-border-shadow"></div>
	<div id="content-bottom-border-shadow"></div>
	<div id="content" class="clearfix">
		
		
		
		<div class="gridWide clearfix">
			
			<h1 class="title">Cotizador en línea</h1>
			
			<?  if (( $_POST['telefono'] == null || $_POST['nombre'] == null || $_POST['apellido'] == null || $_POST['mail'] == null || $_POST['fecha'] == null || validaEmail($_POST['mail']) == false || $_POST['confirmarmail'] == null || validaEmail($_POST['confirmarmail']) == false || count($_SESSION['codigos']) == 0) && isset($_POST['refresh']) ) {  ?>
			<div class="flash notice">
				<h6>Asegurese de llenar todos los campos.</h6>
			</div>
			<? } ?>
			<?  if ( count($_SESSION['codigos']) > 0 && isset ($_POST['telefono']) && ($_POST['nombre']) && isset ($_POST['apellido']) && isset ($_POST['mail']) && isset ($_POST['confirmarmail']) && isset ($_POST['fecha']) &&
			$_POST['mail'] != $_POST['confirmarmail']) {
			?>
			<div class="flash notice">
				<h6>El email no coincide.</h6>
			</div>
			<?
			} 

			if ( count($_SESSION['codigos']) > 0 && isset ($_POST['telefono']) && ($_POST['nombre']) && isset ($_POST['apellido']) && isset ($_POST['mail']) && isset ($_POST['confirmarmail']) && isset ($_POST['fecha']) && 
						$_POST['telefono'] != null && $_POST['nombre'] != null && $_POST['apellido'] != null && $_POST['mail'] != null && $_POST['fecha'] != null && validaEmail($_POST['mail']) == true
						&& $_POST['confirmarmail'] != null && validaEmail($_POST['confirmarmail']) == true && $_POST['mail'] == $_POST['confirmarmail']) { 
			
			$nombre = $_POST['nombre'];
			$telefono = $_POST['telefono'];
			$apellido = $_POST['apellido'];
			$email = $_POST['mail'];
			$fecha = $_POST['fecha'].' 00:00:00';
			$fecha = str_replace('/', '-', $fecha);
			
			$link= Conectarse();
			$sql="INSERT INTO cotizacion (nombre_usuario,apellido_usuario, email_usuario,fecha_evento,fecha_creacion,telefono_usuario) VALUES ('$nombre','$apellido','$email','$fecha', '".date('Y-m-d H:i:s')."', '$telefono')";
			$query=mysql_query($sql,$link);
			$idCotizacion = mysql_insert_id(); 
			
			for ($i = 0; $i < count($_SESSION['codigos']); $i++) { 
			
			$id = $_SESSION['codigos'][$i];
			$post = get_post($id); 
			$titulo = $post->post_title;
			
			//$post_views = get_post_custom($post_id);

			//$post_views = intval($post_views['views'][0]);
			
			$sql="INSERT INTO cotizacion_vestido (codigo,id_cotizacion) VALUES ('$titulo','$idCotizacion')";
			$query=mysql_query($sql,$link);
			}
			
			$cabeceras = "From: contacto@deblanco.cl\r\nContent-type: text/html\r\n";
			
			$id = base64_encode($idCotizacion);  
			$mensaje="
			<div style='background-image:url(http://www.deblanco.cl/wp-content/themes/LeanBiz/images/content-bg.png);border-left: 1px solid #B3B1B1;border-top: 1px solid #B3B1B1;border-right: 1px solid #B3B1B1;width:705px;height:130px;'>
					<img style='margin-left: 30px; margin-top: 10px;' src='http://www.deblanco.cl/wp-content/themes/LeanBiz/images/deblanco/deblanco.png' />
			</div>

			<div style='background-image:url(http://www.deblanco.cl/wp-content/themes/LeanBiz/images/content-bg.png);border-left: 1px solid #B3B1B1;border-right: 1px solid #B3B1B1;width:705px;text-align:center;font-weight:bold;color:#575353;font-size:18px;height: 70px;'>
				Estimada ".ucfirst(strtolower($nombre))." ".ucfirst(strtolower($apellido)).", hemos recibido tu cotizacion en deblanco.cl, para ver en detalle el precio de los vestidos que cotizaste <a style='font-weight:900;' href='http://www.deblanco.cl/cotizacion.php/?idCotizacion=".$id."'>haz click aqui.</a>
			</div>
			<div style='text-align:center; border-left: 1px solid #B3B1B1;border-right: 1px solid #B3B1B1;background-image:url(http://www.deblanco.cl/wp-content/themes/LeanBiz/images/content-bg.png);width:705px;height:auto;'></br></br><a href='http://poodoo.cl/' target='_blank'><img style='border:none;'src='http://www.deblanco.cl/wp-content/themes/LeanBiz/images/poodoo.png' /></a></div>
			<div style='border-left: 1px solid #B3B1B1;border-right: 1px solid #B3B1B1;border-bottom: 1px solid #B3B1B1;background-image:url(http://www.deblanco.cl/wp-content/themes/LeanBiz/images/content-bg.png);width:705px;text-align:center;font-weight:bold;color:#575353;font-size:12px;height: 30px;'>Desarrollado por <a style='text-decoration:none;' href='http://poodoo.cl/' target='_blank'>poodoo.cl</a></div>";
			$remitente = "cotizacion@deblanco.cl";
			$destino= "elvisbrevi@gmail.com";
			$asunto= "Cotizacion";
			$encabezados = "From: $remitente\nReply-To: $remitente\nContent-Type: text/html; charset=iso-8859-1";

			mail($destino, $asunto, $mensaje, $encabezados) or die ("Su mensaje no se envio.");
			
			 ?>
			<!-- Google Code for Solicitud Cotizacion Conversion Page --> <script
			type="text/javascript">
			/* <![CDATA[ */
			var google_conversion_id = 988758419;
			var google_conversion_language = "en";
			var google_conversion_format = "3";
			var google_conversion_color = "ffffff";
			var google_conversion_label = "n2FbCIXbowQQk4O91wM"; var
			google_conversion_value = 0;
			/* ]]> */
			</script>
			<script type="text/javascript"
			src="http://www.googleadservices.com/pagead/conversion.js">
			</script>
			<noscript>
			<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt=""
			src="http://www.googleadservices.com/pagead/conversion/988758419/?value=0&am
			p;label=n2FbCIXbowQQk4O91wM&amp;guid=ON&amp;script=0"/>
			</div>
			</noscript>
			<div class="flash notice">
				<h6>Gracias por cotizar con nosotros</h6>
				<p>Te enviaremos un correo con la cotización a la brevedad.</p></br>
				<a href="http://www.deblanco.cl" style="font-size:14px">Volver a Pagina Principal</a>
			</div>
			<? } else { ?>
			<form name="contacto" method="post" action="http://www.deblanco.cl/catalogo-form/">
			<input type="hidden" name="refresh" value="0"/>
			<table class="cotizador">
			<?

			function callback($buffer)
			{
			  // replace all the apples with oranges
			  return (str_replace("Delete", "", $buffer));
			}
		?>

			<? for ($i = 0; $i < count($_SESSION['codigos']); $i++) { ?>
			<?php
			$id = $_SESSION['codigos'][$i];
			$post = get_post($id); 
			$titulo = $post->post_title;
			?> 
				<tr>
					<td class="row_photo"><? echo get_the_post_thumbnail($id, 'thumbnail'); ?></td>
					<td class="row_name">
						<h6><? echo $titulo ?> <a href="<? echo get_permalink( $id ); ?>" class="aViolet aButton_min iconMore"><span>Ver</span></a></h6>
						<p><!--Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.--></p>
					</td>
					<td class="row_size">
						<select name="talla<? echo $id; ?>" onchange="this.form.action='http://www.deblanco.cl/catalogo-form/?tall=s';this.form.submit()">
							<? 
							if(isset($_POST['talla'.$id])) {
								if ( $_POST['talla'.$id] == "1" ) { $_SESSION['talla'.$id] = "1"; ?> <option selected value="1">Talla S</option> <? } else { ?> <option value="1">Talla S</option> <? }
								if ( $_POST['talla'.$id] == "2" ) { $_SESSION['talla'.$id] = "2"; ?> <option selected value="2">Talla M</option> <? } else { ?> <option value="2">Talla M</option> <? }
								if ( $_POST['talla'.$id] == "3" ) { $_SESSION['talla'.$id] = "3"; ?> <option selected value="3">Talla L</option> <? } else { ?> <option value="3">Talla L</option> <? }
								if ( $_POST['talla'.$id] == "4" ) { $_SESSION['talla'.$id] = "4"; ?> <option selected value="4">Talla XL</option> <? } else { ?> <option value="4">Talla XL</option> <? }
							} else {
							?>
							<option value="1">Talla S</option>
							<option value="2">Talla M</option>
							<option value="3">Talla L</option>
							<option value="4">Talla XL</option>
							<? } ?>
						</select>
					</td>
					<td class="row_delete">
						<a href="http://www.deblanco.cl/quitar.php?indice=<? echo $i; ?>" class="aDelete">Delete</a>
					</td>
				</tr>
			<? }  ?>	
				<tfoot>
					<tr>
						<center><td colspan="4">						
								<div class="clearfix" style="margin-left: 120px;">
								<div class="formOption">
								<input type="button" onclick="window.location='http://www.deblanco.cl/catalogo/'" name="button" id="button" class="aSubmit" value="Ver mas vestidos" style="margin-left:180px;margin-bottom:50px;" />
								</div>
								</div>
								<h1 class="title" style="margin-left: 120px;">Ingresa tus datos de contacto</h1>
								<div class="clearfix" style="margin-left: 120px;">
									<div class="formOption"><input type="text" placeholder="Nombre" name="nombre" id="textfield" /></div>
									<div class="formOption"><input type="text" placeholder="Apellido" name="apellido" id="textfield" /></div>
									<div class="formOption"><input type="text" placeholder="Email" name="mail" id="textfield" onCopy="return false" onPaste="return false" onDrag="return false" onDrop="return false" /></div>
									<div class="formOption"><input type="text" placeholder="Repetir Email" name="confirmarmail" id="textfield" onCopy="return false" onPaste="return false" onDrag="return false" onDrop="return false" /></div>
									<div class="formOption"><input type="text" placeholder="Fecha del Evento" name="fecha" id="datepicker" /></div>
									<div class="formOption"><input onkeypress="return soloLetras(event)" onblur="limpia()" type="text" placeholder="Telefono" name="telefono" id="telefono" /></div>
									<script>
									function soloLetras(e){
										key = e.keyCode || e.which;
										tecla = String.fromCharCode(key).toLowerCase();
										letras = "1234567890";
										especiales = [8,37,39,46];

										tecla_especial = false
										for(var i in especiales){
									 if(key == especiales[i]){
										 tecla_especial = true;
										 break;
											} 
										}
									 
										if(letras.indexOf(tecla)==-1 && !tecla_especial)
											return false;
									}
									</script>
									<div class="formOption"><input type="submit" name="button" id="button" class="aSubmit" value="Solicitar Cotización" /></div>
								</div>			
						</td></center>
					</tr>
				</tfoot>
		
			</table>
		</form>
		<? } ?>
		</div>
		
	</div> <!-- end #content -->
</div> <!-- end #content-border -->	

<?php get_footer(); ?>
<?
function validaEmail($email)
{
if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
	return false;
}
else {
return true;
}
}
?>