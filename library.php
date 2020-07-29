<?php
	/*EJERCICIO LIBRERIA I
Vamos a hacer un ejercicio parecido en donde daremos de alta libros y precios a los cuales asignaremos una id única mediante la función php uniqid().
Para la modificación vamos a utilizar un listener que ejecute una función que busque los campos en la tabla y los traslade al formulario oculto. Para ello utilizaremos las funciones closest() y find() de jquery para recuperar los datos del libro a modificar de la tabla.
Para la baja utilizaremos eventos onclick situados en el propio botón. onclick='baja(\"$id\")'*/

//activar variables de sesion PRIMERA LINEA DE CODIGO!!!!!
session_start();
//eliminamos toda la memoria de la sesion, como limpiar array de sesion
//session_destroy();

//ini vars
$mensaje = '';
$titulo = '';
$precio = '';
$libreria = [];
$repetido = false;

//guardar datos de sesion
if(isset($_SESSION['libreria'])){
		$libreria = $_SESSION['libreria'];
}

/*******************************/
/****** ALTA DE LIBRO ********/
/*******************************/
function tituloRepetido($titulo){
	global $libreria;
	foreach ($libreria as $key => $libro) {
		if ($titulo == $libro['titulo']) {
			return true;
		}
	}
	return false;
}

if(isset($_POST['alta'])){
	//recupera datos libro formulario limpiando espacios por delante y por detras
	$id_libro = uniqid();
	$titulo = trim($_POST['titulo']);
	$precio = trim($_POST['precio']);

	//validar datos
	try{
		if($titulo == ''){
			throw new Exception("Título obligatorio", 10);
		}
		if($precio == ''){
			throw new Exception("Precio obligatorio", 10);
		}
		//comprueba libro no repetido
		$repetido = tituloRepetido($titulo);
		if($repetido){
			throw new Exception("Título repetido", 10);
		}

		//una vez validados los datos damos de alta en el array de libros
		$libreria[$id_libro]['titulo'] = $titulo;
		$libreria[$id_libro]['precio'] = $precio;
		$mensaje = 'Alta efectuada con éxito';
		//limpìar datos
		$id_libro = '';
		$titulo = '';
		$precio = '';
		print_r($libreria);


	} catch (Exception $e){
			//tratamiento de errores
			$mensaje = $e->getMessage();
	}
}

/*******************************/
/****** BAJA DE 1 LIBRO ********/
/*******************************/
if(isset($_POST['bajalibro'])){
	//recuperar el id
	$id_baja = $_POST['id_baja'];

	//borrar libro, borrar fila del array
	unset($libreria[$id_baja]);
	$mensaje_baja = 'baja de 1 libro efectuada con éxito';
}

/************************************/
/****** CONSULTA LIBROS *************/
/************************************/
//creamos tr para ir mostrando fila a fila el contenido de libros
$tr = '';
print_r($libreria);
//ordenar por titulo
//asort($libreria);

//ordenar por precio
$lista_precios = [];
//foreach reducido
foreach ($libreria as $libro) {
	array_push($lista_precios, $libro['precio']);
}
array_multisort($lista_precios, SORT_ASC, $libreria);


foreach ($libreria as $key_id => $libro) {
	//<tr><td>ID</td><td>Titulo</td><td>Precio</td><td></td></tr>
	$tr.="<tr>";
	$tr.="<td class='id_libro'>$key_id</td>";
	//añadimos el input type a text para que sea editable
	$tr.="<td>$libro[titulo]</td>";
	$tr.="<td>$libro[precio]</td>";
	$tr.="<td>";
	//enviamos al servidor el valor dl id que queremos dar de baja, oculto para el usuario, visible para el servidor HIDDEN
	$tr.="<form method='post' action='#'>";
	$tr.="<input type='hidden' value='$key_id' name='id_baja'>";
	$tr.="<input type='submit' name='bajalibro' value='baja libro'>";
	$tr.="</form>";
	$tr.= "</td>";
	$tr.="</tr>";

	
}

//guardar el array de personas en la variable de sesion para no perderlo y que actue como la variable sesion que no se elimina cuando refrescas...
$_SESSION['libreria'] = $libreria;
$libreria = [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<style type="text/css">
		div.container {
			margin: auto; width:920px; text-align: center;
		}
		table {
			border: 5px ridge blue;
			width: 800px;
		}
		th, td {
			background:white; width:auto; border: 2px solid green; text-align: left;
		}
		input[type=text] {width: 330px;}
		form, table {margin:auto}
	</style>
	<script type="text/javascript" src='https://code.jquery.com/jquery-3.1.1.min.js'></script>
</head>
<body>
	<div class="container">
		<h2 style="text-align:center">Library</h2>
		<!--zona de mensajes-->
		<span><?=$mensaje?></span><br><br>
		<!--Form Alta Libro-->
		<form name="formularioalta" method="post" action="#">
			<table border='2'>
				<tr><th>Título</th><th>Precio</th><th colspan='2' style='width:150px'>Opción</th></tr>
				<tr>
				<td><input type='text' size='50' maxlenght='100' name='titulo' value='<?=$titulo?>'/></td>
				<td><input type='number' maxlenght='5' name='precio' value='<?=$precio?>'/></td>
				<td colspan='2'><input type='submit' name='alta' value='Agregar' /></td>
				</tr>
			</table>
		</form><br>
		<div>
			<table>
				<tr><th>ID</th><th>Título</th><th>Precio</th><th></th></tr>
			<?=$tr;?>
			</table>
		</div>
	</div>
</body>
</html>