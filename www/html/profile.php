<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="img/cards_icon.png" type="image/x-icon">
<title>Perfíl</title>
<link rel="stylesheet" type="text/css" href="css/std.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
input[type="radio"] {
  display: none;
}

label {
  color: grey;
}

.clasificacion {
  direction: rtl;
  unicode-bidi: bidi-override;
}


input[type="radio"]:checked ~ label {
  color: orange;
}
</style>
</head>

<body>

<?php
  //comprueba sesion de usuario estandar
  require("../checkStd.php");
  require('../connection.php');

  try{
      
      $result=$client->run(
		'MATCH (user:User)
		WHERE user.email = $email		
		 RETURN user',array('email'=>$_SESSION["user"])
	);

      $row=0;

      $total=$client->run(
		'MATCH (albums:Album)<-[:VOTED]-(user:User)
		 WHERE user.email=$email	
		 RETURN count(albums) as total',array('email'=>$_SESSION["user"])
	);

      $paginas=ceil($total[0]->get('total')/60);

      $inicio=0;

      if(isset($_GET['pagina'])){
	$inicio=$_GET['pagina']-1;	
      }
      $inicio=$inicio*60;
      $albums=$client->run(
		'MATCH (bands:Band)<-[:FROM_ARTIST]-(albums:Album)-[:GENRE_IS]->(genres:Genre), (bands:Band)-[:FROM_COUNTRY]->(country:Country), (albums:Album)<-[:VOTED]-(user:User)
		 WHERE user.email=$email	
		 RETURN albums, bands, genres, country
		 SKIP '.$inicio.' LIMIT 60',array('email'=>$_SESSION["user"])
	);

    }catch(Exception $e){
      die("Error: " . $e->GetMessage());
    }

?>

<ul class="topnav">

  <li><a class="active" href="profile.php">Perfil</a></li>
  <li><a href="selector.php">Selector</a></li>
  <li><a href="members.php">Miembros</a></li>
  <li><a href="random.php">Aleatorio</a></li>
  <li><a>Puntos: <?php foreach($result as $user){
						echo $user->get('user')->points;
					} ?></a></li>
  <li class="right"><a href="php/close.php">Cerrar sesión</a></li>
</ul>
  
    <h1>Perfil</h1>
     
    <table width="50%" border="0" align="center">

      <tr>
        <td colspan="10" class="tableHeader"> Discos </td>

      </tr>
      <tr>

      <?php foreach($albums as $album): if($row==10): $row=0;?>
        </tr>
        <tr>
      <?php endif;?>

      <td<?php echo ' class="purchased"';?>><a href="detail.php?album=<?php echo $album["albums"]->name; ?>&band=<?php echo $album["bands"]->name; ?>"><img src="<?php $row+=1; echo $album["albums"]->image; ?>" width="200" height="200"></a><?php echo '<br> Album: '.$album["albums"]->name; ?>

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
	<form>
	  <p class="clasificacion">
	    <input id="radio1" type="radio" name="estrellas" disabled value="5" <?php if($val==5){echo 'checked';}?>><!--
	    --><label for="radio1">★</label><!--
	    --><input id="radio2" type="radio" name="estrellas" disabled value="4" <?php if($val==4){echo 'checked';}?>><!--
	    --><label for="radio2">★</label><!--
	    --><input id="radio3" type="radio" name="estrellas" disabled value="3" <?php if($val==3){echo 'checked';}?>><!--
	    --><label for="radio3">★</label><!--
	    --><input id="radio4" type="radio" name="estrellas" disabled value="2" <?php if($val==2){echo 'checked';}?>><!--
	    --><label for="radio4">★</label><!--
	    --><input id="radio5" type="radio" name="estrellas" disabled value="1" <?php if($val==1){echo 'checked';}?>><!--
	    --><label for="radio5">★</label>
	  </p>
	</form>
      </td>  

      <?php endforeach;?>
      </tr>
      <tr><td colspan="10"> 
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