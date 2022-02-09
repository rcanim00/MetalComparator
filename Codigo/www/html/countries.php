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

    try{

      $tsx = $client->beginTransaction();

	$tsx->run(
		'CREATE (:Country {name: $name})', 
		array('name'=>$name)
	);


	$tsx->commit();


    }catch(Exception $e){

      if (isset($tsx)) { 
          $tsx->rollback();
          $tsx=null;
      }
      
    }
    header("Location:countries.php");
  }

?>

<ul class="topnav">
  <li><a href="users.php">Usuarios</a></li>
  <li><a class="active" href="countries.php">Paises</a></li>
  <li><a href="genres.php">Géneros</a></li>
  <li><a href="bands.php">Bandas</a></li>
  <li><a href="albums.php">Albums</a></li>
  <li class="right"><a href="php/close.php">Cerrar sesión</a></li>
</ul>

<h1>Paises</h1>
  
  <table width="50%" border="0" align="center">
    <tr >
      <td class="tableHeader">Nombre</td>
      <td class="invisible">&nbsp;</td>
      <td class="invisible">&nbsp;</td>
    </tr> 
   
		<?php 

    try{
      $total=$client->run(
		'MATCH (country:Country)	
		 RETURN count(country) as total'
	);

      $paginas=ceil($total[0]->get('total')/100);

      $inicio=0;

      if(isset($_GET['pagina'])){
	$inicio=$_GET['pagina']-1;	
      }
      $inicio=$inicio*100;

      $result=$client->run(
		'MATCH (countries:Country) 
		RETURN countries
		SKIP '.$inicio.' LIMIT 100'
	);

    }catch(Exception $e){
      die("Error: " . $e->GetMessage());
    }

    foreach($result as $country):?>
    

    <tr>
     
      <td> <?php echo $country->get('countries')->name; ?> </td>
 
      <td class="button"><a href="php/deleteCountry.php?Id=<?php echo $country->get('countries')->name; ?>"><input type="image" src="img/trash.png" width="25" height="25" name="delete" id="delete" value="Borrar"></a></td>

      <td class="button"><a href="php/updateCountry.php?name=<?php echo $country->get('countries')->name; ?>"><input type="image" src="img/update.png"  width="25" height="25" name="update" id="update" value="Actualizar"></a></td>
    </tr>    

    <?php
    endforeach;
    ?>

      
	<tr>

      <form method="post" action=<?php echo $_SERVER["PHP_SELF"]; ?> >
      <td><input type="text" name="name" size="40" class="centered" required></td>
      <td class="button"><input class="addButton" type="submit" name="add" id="add" value=""></td></tr>    
      </form>
	<tr><td> 
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