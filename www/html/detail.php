<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="img/cards_icon.png" type="image/x-icon">
<title>Perfíl</title>
<link rel="stylesheet" type="text/css" href="css/std.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
input[type = "radio"]{ display:none;/*position: absolute;top: -1000em;*/}
label{ color:grey;}

.clasificacion{
    direction: rtl;
    unicode-bidi: bidi-override;
}

label:hover,
label:hover ~ label{color:orange;}
input[type = "radio"]:checked ~ label{color:orange;}
</style>
</head>

<body>

<?php
  //comprueba sesion de usuario estandar
  require("../checkStd.php");
  require('../connection.php');
  
  if(isset($_POST["valorar"],$_POST["estrellas"])){
	
	try{
		$client->run(
			'MATCH (album:Album)-[:FROM_ARTIST]->(band:Band)
			WHERE album.name = $name AND band.name = $band
			SET album += {
				points: $points,
				number: $number
			}',array('name'=>$_POST["album"],'band'=>$_POST["band"],'points'=> $_POST["rate"] + $_POST["estrellas"], 'number'=> $_POST["number"]+1)
		);


		$client->run(
			'MATCH (band:Band)<-[:FROM_ARTIST]-(album:Album)<-[rel:VOTED]-(user:User)
			WHERE album.name = $name AND user.email = $email AND band.name = $band
			DELETE rel',array('name'=>$_POST["album"],'email'=>$_SESSION["user"],'band'=>$_POST["band"])
		);

		$client->run(
			'MATCH (band:Band)<-[:FROM_ARTIST]-(album:Album),(user:User)
			WHERE album.name = $name AND user.email = $email AND band.name = $band
			SET user += {
				points: $points
			}
			CREATE (user)-[:VOTED]->(album)',array('name'=>$_POST["album"],'band'=>$_POST["band"],'email'=>$_SESSION["user"],'points'=>$_POST['points']+1)
		);
	}catch(Exception $e){
	      die("Error: " . $e->GetMessage());
	}
		
  }

  if(!isset($_GET["album"],$_GET["band"])){
	header("Location:profile.php");
  }

  try{
      
      $result=$client->run(
		'MATCH (user:User)
		WHERE user.email = $email		
		 RETURN user',array('email'=>$_SESSION["user"])
	);

      $albums=$client->run(
		'MATCH (bands:Band)<-[:FROM_ARTIST]-(albums:Album)-[:GENRE_IS]->(genres:Genre), (bands:Band)-[:FROM_COUNTRY]->(country:Country)
		 WHERE albums.name = $album AND bands.name = $band 
		 RETURN albums, bands, genres, country',array('album'=>$_GET['album'],'band'=>$_GET['band'])
	);


    }catch(Exception $e){
      die("Error: " . $e->GetMessage());
    }

?>

<ul class="topnav">

  <li><a href="profile.php">Perfil</a></li>
  <li><a href="selector.php">Selector</a></li>
  <li><a href="members.php">Miembros</a></li>
  <li><a href="random.php">Aleatorio</a></li>
  <li><a>Puntos: <?php foreach($result as $user){
						echo $points=$user->get('user')->points;
					} ?></a></li>
  <li class="right"><a href="php/close.php">Cerrar sesión</a></li>
</ul>
  
    <h1>Discos</h1>
     
    <table width="50%" border="0" align="center">

      <tr>
        <td colspan="10" class="tableHeader"> Discos </td>

      </tr>
      <tr>

      <?php foreach($albums as $album):	?>

      <td<?php echo ' class="purchased"';?>><a href="detail.php?album=<?php echo $album["albums"]->name; ?>&band=<?php echo $album["bands"]->name; ?>"><img src="<?php $row+=1; echo $album["albums"]->image; ?>" width="400" height="400"></a>

	<?php echo '<br> Album: '.$album["albums"]->name; ?>
	<?php echo '<br> Artista: '.$album["bands"]->name; ?>
	<?php echo '<br> País: '.$album["country"]->name; ?>
	<?php echo '<br> Género: '.$album["genres"]->name; 
	$sec=$client->run(
			'MATCH (albums:Album)-[:SECOND_GENRE_IS]->(genres:Genre)
			 WHERE albums.name = $name			
			 RETURN genres',array('name'=>$album['albums']->name)
		);

	foreach($sec as $gen):
	echo '<br> Otros: '.$gen->get('genres')->name;
	endforeach;
	$val=intval($album["albums"]->points/$album["albums"]->number);
	?>
	<form method="post" action=<?php echo $_SERVER["PHP_SELF"]; ?>>
	  <p class="clasificacion">

	    <input type="hidden" name="points" id="points" value="<?php echo $points; ?>">
	    <input type="hidden" name="rate" id="rate" value="<?php echo $album["albums"]->points;?>">
	    <input type="hidden" name="number" id="number" value="<?php echo $album["albums"]->number; ?>">
	    <input type="hidden" name="album" id="album" value="<?php echo $album["albums"]->name; ?>">
	    <input type="hidden" name="band" id="band" value="<?php echo $album["bands"]->name; ?>">

	    <input id="radio1" type="radio" name="estrellas" value="5" <?php if($val==5){echo 'checked';}?>><!--
	    --><label for="radio1">★</label><!--
	    --><input id="radio2" type="radio" name="estrellas" value="4" <?php if($val==4){echo 'checked';}?>><!--
	    --><label for="radio2">★</label><!--
	    --><input id="radio3" type="radio" name="estrellas" value="3" <?php if($val==3){echo 'checked';}?>><!--
	    --><label for="radio3">★</label><!--
	    --><input id="radio4" type="radio" name="estrellas" value="2" <?php if($val==2){echo 'checked';}?>><!--
	    --><label for="radio4">★</label><!--
	    --><input id="radio5" type="radio" name="estrellas" value="1" <?php if($val==1){echo 'checked';}?>><!--
	    --><label for="radio5">★</label>
	  </p>
	  <input type="submit" name="valorar" id="valorar" value="Valorar">
	</form>
      </td>  

      <?php endforeach;?>
      </tr> 

    </table>

</body>
</html>