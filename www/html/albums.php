<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="img/cards_icon.png" type="image/x-icon">
<title>CRUD</title>
<link rel="stylesheet" type="text/css" href="css/crud.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

<?php

  require("../checkAdmin.php");
  require('../connection.php');

  if(isset($_POST["add"])){

    $name=$_POST["name"]; //Almacena el valor del name 
    $image=$_POST["image"];

    try{

      $tsx = $client->beginTransaction();

	$tsx->run(
		'MATCH (genres:Genre),(bands:Band)
		WHERE genres.name = $gName AND bands.name = $bName
		CREATE (bands)<-[:FROM_ARTIST]-(:Album {name: $name, image: $image, points: 0, number: 0})-[:GENRE_IS]->(genres)', 
		array('name'=>$name,'image'=>$image,'gName'=>$_POST["genre"],'bName'=>$_POST["band"])
	);

	if(isset($_POST["secgenre"])){

		$tsx->run(
			'MATCH (genres:Genre), (albums:Album)
			WHERE genres.name = $gName AND albums.name = $aName
			CREATE (albums)-[:SECOND_GENRE_IS]->(genres)', 
			array('gName'=>$_POST["secgenre"],'aName'=>$_POST["name"])
		);
	
	}


	$tsx->commit();


    }catch(Exception $e){

      if (isset($tsx)) { 
          $tsx->rollback();
          $tsx=null;
      }
      
    }
    header("Location:albums.php");
  }

?>

<ul class="topnav">
  <li><a href="users.php">Usuarios</a></li>
  <li><a href="countries.php">Paises</a></li>
  <li><a href="genres.php">Géneros</a></li>
  <li><a href="bands.php">Bandas</a></li>
  <li><a class="active" href="albums.php">Albums</a></li>
  <li class="right"><a href="php/close.php">Cerrar sesión</a></li>
</ul>

<h1>Albums</h1>
  
  <table width="95%" border="0" align="center">
    <tr >
      <td class="tableHeader">Artista</td>
      <td class="tableHeader">Género</td>
      <td class="tableHeader">Segundo Genero</td>
      <td class="tableHeader">Nombre</td>
      <td class="tableHeader">Imagen</td>
      <td class="invisible">&nbsp;</td>
      <td class="invisible">&nbsp;</td>
    </tr> 
   
		<?php 

    try{

	$total=$client->run(
		'MATCH (album:Album)	
		 RETURN count(album) as total'
	);

      $paginas=ceil($total[0]->get('total')/100);

      $inicio=0;

      if(isset($_GET['pagina'])){
	$inicio=$_GET['pagina']-1;	
      }
      $inicio=$inicio*100;

      $result=$client->run(
			'MATCH (bands:Band)<-[:FROM_ARTIST]-(albums:Album)-[:GENRE_IS]->(genres:Genre)			
			 RETURN albums, bands, genres
			 SKIP '.$inicio.' LIMIT 100'
		);

    }catch(Exception $e){
      die("Error: " . $e->GetMessage());
    }

    foreach($result as $album):?>
    

    <tr>
     
      <td> <?php echo $album->get('bands')->name; ?> </td>

      <td> <?php echo $album->get('genres')->name; ?> </td>

      <td> <?php 
	
	$sec=$client->run(
			'MATCH (albums:Album)-[:SECOND_GENRE_IS]->(genres:Genre)
			 WHERE albums.name = $name			
			 RETURN genres',array('name'=>$album->get('albums')->name)
		);

	foreach($sec as $gen):
	echo $gen->get('genres')->name;
	endforeach;
	    ?>
	</td>

      <td> <?php echo $album->get('albums')->name; ?> </td>

      <td> <?php echo $album->get('albums')->image; ?> </td>
 
      <td class="button"><a href="php/deleteAlbum.php?Id=<?php echo $album->get('albums')->name; ?>"><input type="image" src="img/trash.png" width="25" height="25" name="delete" id="delete" value="Borrar"></a></td>

      <td class="button"><a href="php/updateAlbum.php?name=<?php echo $album->get('albums')->name; ?>&image=<?php echo $album->get('albums')->image; ?>&band=<?php echo $album->get('bands')->name; ?>&genre=<?php echo $album->get('genres')->name; foreach($sec as $gen):
	echo '&sec='.$gen->get('genres')->name;
	endforeach; ?>"><input type="image" src="img/update.png"  width="25" height="25" name="update" id="update" value="Actualizar"></a></td>
    </tr>    

    <?php
    endforeach;
    ?>

      
	<tr>

      <form method="post" action=<?php echo $_SERVER["PHP_SELF"]; ?> >
      <td><input type="text" name="band" size="30" class="centered" required></td>
      <td><input type="text" name="genre" size="30" class="centered" required></td>
      <td><input type="text" name="secgenre" size="30" class="centered"></td>
      <td><input type="text" name="name" size="40" class="centered" required></td>	
      <td><input type="text" name="image" size="30" class="centered" required></td>
      <td class="button"><input class="addButton" type="submit" name="add" id="add" value=""></td></tr>    
      </form>
	<tr><td colspan="5"> 
	<?php for($i=1;$i<=$paginas;$i++){
		  if($i%20==0){
			echo '<br>';
		  }
		  echo '<a href="?pagina='.$i.'"> '.$i.' </a>';
		}?>
      </td></tr>
  </table>

  

</body>
</html>
