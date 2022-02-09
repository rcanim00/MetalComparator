<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>CRUD</title>
<link rel="shortcut icon" href="/img/cards_icon.png" type="image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="/css/crud.css">
</head>

<body>

  <?php
  
  /* Actualiza el usuario si la contraseña es correcta y se les añadirá unos nuevos valores tanto al identificador, como al email,
  fecha de nacimiento, contraseña, nombre y apellidos. Actualizará la base de datos y el usuario estará actualizado con sus nuevos valores. */

  require("../../checkAdmin.php");
  require("../../connection.php");

  ?>

<ul class="topnav">
  <li><a href="/users.php">Usuarios</a></li>
  <li><a href="/countries.php">Paises</a></li>
  <li><a href="/genres.php">Géneros</a></li>
  <li><a href="/bands.php">Bandas</a></li>
  <li><a href="/albums.php">Albums</a></li>
  <li class="right"><a href="close.php">Cerrar sesión</a></li>
</ul>

<h1>Actualizar Género</h1>


<?php
  

if(!isset($_POST["update"])) {

	if(!isset($_GET["name"])){ 
		header("Location:/genres.php");
	}

	$name=$_GET["name"];

}else{

    try{

      $tsx=$client->beginTransaction();

      
	$tsx->run(
		'MATCH(genre:Genre) 
		WHERE genre.name = '."'".$_POST["id"]."'".'
		SET genre += {
			name: $name
		}', array('name'=>$_POST["name"])
	);
	

      $tsx->commit();

    }catch(Exception $e){

      if (isset($tsx)) { 
          $tsx->rollback();
          $tsx=null;
      }
      
    }

    header("Location:/genres.php");

  }
  
?>


<form method="post" action=<?php echo $_SERVER["PHP_SELF"]; ?>>

  <input type="hidden" name="id" id="id" value="<?php echo $name; ?>">

  <table width="50%" border="0" align="center">
    
    <tr>
      <td>Nombre</td>
      <td><label for="name"></label>
      <input type="text" name="name" id="name" value="<?php echo $name; ?>" required></td>
    </tr>
    

    <tr>
      <td colspan="2"><input type="submit" name="update" id="update" value="Actualizar"></td>
    </tr>
  </table>
</form>

</body>
</html>
