<? 
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

$idCotizacion = base64_decode($_GET["idCotizacion"]);

$link= Conectarse();

//cotizacion
$sql="select * from cotizacion where id_cotizacion='$idCotizacion'";
$query=mysql_query($sql,$link);

$nombre;$apellido;$email;$visitas;

while ($fila = mysql_fetch_array($query, MYSQL_NUM)) {
	$nombre=$fila[1];
	$apellido=$fila[2];
	$email=$fila[3];
	$visitas=$fila[7];
}

// contar visita
if ($visitas == null) { $visitas = 1; } else { $visitas++; }
$sql="update cotizacion set visitas='$visitas' where id_cotizacion='$idCotizacion'";
$query=mysql_query($sql,$link);

// cotizacion-vestido
$sql="
	select cotizacion_vestido.codigo, vestido.imagen_url, vestido.precio_venta, vestido.precio_arriendo, vestido.descuento_venta, 
	vestido.descuento_arriendo, vestido.talla_s, vestido.talla_m, vestido.talla_l, vestido.talla_xl
	from cotizacion_vestido, vestido 
	where cotizacion_vestido.id_cotizacion='$idCotizacion' and cotizacion_vestido.codigo=vestido.codigo ";
	
$query=mysql_query($sql,$link) or die( "Error en $consulta: " . mysql_error() );

$vestidos = array();
$indice = 0;

while ($fila = mysql_fetch_array($query, MYSQL_NUM)) {
	$vestidos[$indice][0] = $fila[0]; // codigo
	$vestidos[$indice][1] = $fila[1]; // imagen
	$vestidos[$indice][2] = $fila[2]; // precio venta
	$vestidos[$indice][3] = $fila[3]; // precio arriendo
	$vestidos[$indice][4] = $fila[4]; // descuento venta
	$vestidos[$indice][5] = $fila[5]; // descuento arriendo
	$vestidos[$indice][6] = $fila[6]; // talla s
	$vestidos[$indice][7] = $fila[7]; // talla m
	$vestidos[$indice][8] = $fila[8]; // talla l
	$vestidos[$indice][9] = $fila[9]; // talla xl
	$indice++;
}

?> 
<style type="text/css">
@font-face { font-family: "Museo1"; src: url(http://www.deblanco.cl/wp-content/themes/LeanBiz/fonts/Museo_Slab_100.ttf); }
@font-face { font-family: "Museo2"; src: url(http://www.deblanco.cl/wp-content/themes/LeanBiz/fonts/Museo_Slab_700.ttf); }
</style>
<!--[if IE]>
<style>
#tabla { width:705px; }
</style>
<!-- <![endif]-->
<body style="background-color:gray">
<div id="cabeza" style="margin-left: auto;margin-right: auto;background-image:url('http://www.deblanco.cl/wp-content/themes/LeanBiz/images/content-bg.png');border-left: 1px solid #B3B1B1;border-top: 1px solid #B3B1B1;border-right: 1px solid #B3B1B1;width:705px;height:130px;">
		<img style="margin-left:30px; margin-top:10px;" id="logo" src="http://www.deblanco.cl/wp-content/themes/LeanBiz/images/deblanco/deblanco.png" />
		<a href="https://www.facebook.com/pages/Deblanco/248518248531316" target="_blank" ><img style="cursor:pointer;margin-left: 200px;border:none;"  id="facebook" src="http://www.deblanco.cl/wp-content/themes/LeanBiz/images/deblanco/spriteFb_hover2.png" /></a>
</div>

<div id="saludo" style="margin-left: auto;margin-right: auto;border-left: 1px solid #B3B1B1;border-right: 1px solid #B3B1B1;background-image:url('http://www.deblanco.cl/wp-content/themes/LeanBiz/images/content-bg.png');width:705px;text-align:center;font-family:'Museo1';font-weight:bold;color:#575353;font-size:18px;height: 70px;">
	Estimada <?=ucfirst(strtolower($nombre))?> <?=ucfirst(strtolower($apellido))?>, estos son los precios de los vestidos que usted cotizo en nuestro sitio:
</div>
<table id="tabla" style="margin-left: auto;margin-right: auto;background-image:url('http://www.deblanco.cl/wp-content/themes/LeanBiz/images/content-bg.png');width:707px;text-align:center; border-left: 1px solid #B3B1B1;border-right: 1px solid #B3B1B1;">
	<tr id="primera-fila" style="font-family:'Museo2';font-weight:bold;color:pink;font-size:20px;text-shadow: 1px 1px #C55A88;">
		<td>Imagen</td>
		<td>Tallas</td>
		<td>Precio de Venta</td>
		<td>Precio de Arriendo</td>
	</tr>
	<? for ($i = 0; $i < count($vestidos); $i++) { ?>
	<tr id="fila-secundaria" style="font-family:'Museo1'; font-weight:bold; color:gray; font-size:20px; text-shadow: 1px 1px black;">
		<td><img style="border-radius:10px;" src="<?=$vestidos[$i][1]?>" width="70" height="70" /></td>
		<td><? 
			$tallas="";
			
			if ($vestidos[$i][6] == "s") {
				$tallas.="S";
			}
			
			if ($vestidos[$i][7] == "s") {
				if ($tallas=="") { $tallas.="M"; } else { $tallas.=", M"; }
			}
			
			if ($vestidos[$i][8] == "s") {
				if ($tallas=="") { $tallas.="L"; } else { $tallas.=", L"; }
			}
			
			if ($vestidos[$i][9] == "s") {
				if ($tallas=="") { $tallas.="XL"; } else { $tallas.=", XL"; }
			}
			
			echo $tallas;
		?></td>
		<td>$<?=number_format((($vestidos[$i][2]*$vestidos[$i][4])/100), 0, '', '.')?></td>
		<td>$<?=number_format((($vestidos[$i][3]*$vestidos[$i][5])/100), 0, '', '.')?></td>
	</tr>
	<? } ?>
</table>
<div id="logo-poodoo" style="margin-left: auto;margin-right: auto;text-align:center; border-left: 1px solid #B3B1B1;border-right: 1px solid #B3B1B1;background-image:url('http://www.deblanco.cl/wp-content/themes/LeanBiz/images/content-bg.png');width:705px;height:auto;"></br></br><a href="http://poodoo.cl/" target="_blank"><img style="border:none;" id="poodoo" src="http://www.deblanco.cl/wp-content/themes/LeanBiz/images/poodoo.png" /></a></div>
<div id="creditos" style="margin-left: auto;margin-right: auto;border-left: 1px solid #B3B1B1;border-right: 1px solid #B3B1B1;border-bottom: 1px solid #B3B1B1;background-image:url('http://www.deblanco.cl/wp-content/themes/LeanBiz/images/content-bg.png');width:705px;text-align:center;font-family:'Museo1';font-weight:bold;color:#575353;font-size:12px;height: 30px;">Desarrollado por <a style="text-decoration:none;" href="http://poodoo.cl/" target="_blank">poodoo.cl</a></div>
</body>