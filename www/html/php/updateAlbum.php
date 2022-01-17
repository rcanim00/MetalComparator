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

<h1>Actualizar Album</h1>


<?php
  

if(!isset($_POST["update"])) {

	if(!isset($_GET["name"],$_GET["image"],$_GET["band"],$_GET["genre"])){ 
		header("Location:/albums.php");
	}

	$name=$_GET["name"];
	$image=$_GET["image"];
	$band=$_GET["band"];
	$genre=$_GET["genre"];

}else{

    try{

      $tsx=$client->beginTransaction();

      
	
	
	$tsx->run(
		'MATCH (:Band)<-[rel:FROM_ARTIST]-(album:Album)-[reltwo:GENRE_IS]->(:Genre)	
		WHERE album.name = $name
		DELETE rel, reltwo', array('name'=>$_POST["id"])
	);

	$tsx->run(
		'MATCH (genres:Genre),(bands:Band), (albums:Album)
		WHERE genres.name = $gName AND bands.name = $bName AND albums.name = $name
		CREATE (bands)<-[:FROM_ARTIST]-(albums)-[:GENRE_IS]->(genres)', 
		array('name'=>$_POST["id"],'gName'=>$_POST["genre"],'bName'=>$_POST["band"])
	);

	if(isset($_POST["secgenre"])){

		$tsx->run(
			'MATCH (album:Album)-[rel:SECOND_GENRE_IS]->(:Genre)	
			WHERE album.name = $name
			DELETE rel', array('name'=>$_POST["id"])
		);

		$tsx->run(
			'MATCH (genres:Genre), (albums:Album)
			WHERE genres.name = $gName AND albums.name = $name
			CREATE (albums)-[:SECOND_GENRE_IS]->(genres)', 
			array('gName'=>$_POST["secgenre"],'name'=>$_POST["id"])
		);
		
	}

	$tsx->run(
		'MATCH(album:Album) 
		WHERE album.name = '."'".$_POST["id"]."'".'
		SET album += {
			name: $name,
			image: $image
		}', array('name'=>$_POST["name"],'image'=>$_POST["image"])
	);

      $tsx->commit();

    }catch(Exception $e){

      if (isset($tsx)) { 
          $tsx->rollback();
          $tsx=null;
      }
      
    }

    header("Location:/albums.php");

  }
  
?>


<form method="post" action=<?php echo $_SERVER["PHP_SELF"]; ?>>

  <input type="hidden" name="id" id="id" value="<?php echo $name; ?>">

  <table width="50%" border="0" align="center">
    
    <tr>
    <td>Banda</td>
    <td><label for="band"></label>
    <input type="text" name="band" value="<?php echo $band; ?>" required>
    </tr>

    <tr>
    <td>Género</td>
    <td><label for="genre"></label>
    <input type="text" name="genre" value="<?php echo $genre; ?>" required>
    </tr>

    <tr>
    <td>Segundo Género</td>
    <td><label for="secgenre"></label>
    <input type="text" name="secgenre" value="<?php echo $_GET["sec"]; ?>">
    <tr>

    <tr>
      <td>Nombre</td>
      <td><label for="name"></label>
      <input type="text" name="name" id="name" value="<?php echo $name; ?>" required></td>
    </tr>
    
    <tr>
      <td>Imagen</td>
      <td><label for="status"></label>
      <input type="text" name="image" id="image" value="<?php echo $image; ?>" required></td>
    </tr>

    <tr>
      <td colspan="2"><input type="submit" name="update" id="update" value="Actualizar"></td>
    </tr>
  </table>
</form>

</body>
</html>
