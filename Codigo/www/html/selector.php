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
      $arg=0;

	$statement='MATCH (bands:Band)<-[:FROM_ARTIST]-(albums:Album)-[:GENRE_IS]->(genres:Genre), (bands:Band)-[:FROM_COUNTRY]->(country:Country) ';

	if(isset($_POST['generoSec'])){
		if($_POST['generoSec']!="0"){
			$arg++;
			$statement=$statement.",(albums:Album)-[:SECOND_GENRE_IS]->(sec:Genre) WHERE sec.name = "."'".$_POST['generoSec']."' ";	
		}
	}else{
		if(isset($_GET['generoSec'])){
			if($_GET['generoSec']!="0"){
				$arg++;
				$statement=$statement.",(albums:Album)-[:SECOND_GENRE_IS]->(sec:Genre) WHERE sec.name = "."'".$_GET['generoSec']."' ";	
			}
		}
	}

	if(isset($_POST['genero'])){
		if($_POST['genero']!="0"){
			if($arg==0){
				$arg++;
				$statement=$statement." WHERE genres.name = "."'".$_POST['genero']."' ";	
			}else{
				$statement=$statement." AND genres.name = "."'".$_POST['genero']."' ";	
			}
			
		}
	}else{
		if(isset($_GET['genero'])){
			if($_GET['genero']!="0"){
				if($arg==0){
					$arg++;
					$statement=$statement." WHERE genres.name = "."'".$_GET['genero']."' ";	
				}else{
					$statement=$statement." AND genres.name = "."'".$_GET['genero']."' ";	
				}
				
			}
		}
	}

	if(isset($_POST['estado'])){
		if($_POST['estado']=="1"){
			if($arg==0){
				$arg++;
				$statement=$statement." WHERE bands.status = 'Active' ";	
			}else{
				$statement=$statement." AND bands.status = 'Active' ";	
			}
			
		}else if($_POST['estado']=="2"){
			if($arg==0){
				$arg++;
				$statement=$statement." WHERE bands.status = 'Split-up' ";	
			}else{
				$statement=$statement." AND bands.status = 'Split-up' ";	
			}
			
		}else if($_POST['estado']=="3"){
			if($arg==0){
				$arg++;
				$statement=$statement." WHERE bands.status = 'Changed name' ";	
			}else{
				$statement=$statement." AND bands.status = 'Changed name' ";	
			}
			
		}else if($_POST['estado']=="4"){
			if($arg==0){
				$arg++;
				$statement=$statement." WHERE bands.status = 'Unknown' ";	
			}else{
				$statement=$statement." AND bands.status = 'Unknown' ";	
			}
			
		}else if($_POST['estado']=="5"){
			if($arg==0){
				$arg++;
				$statement=$statement." WHERE bands.status = 'On hold' ";	
			}else{
				$statement=$statement." AND bands.status = 'On hold' ";	
			}
			
		}else if($_POST['estado']=="6"){
			if($arg==0){
				$arg++;
				$statement=$statement." WHERE bands.status = 'Disputed' ";	
			}else{
				$statement=$statement." AND bands.status = 'Disputed' ";	
			}
			
		}
	}else{
		if(isset($_GET['estado'])){
			if($_GET['estado']=="1"){
				if($arg==0){
					$arg++;
					$statement=$statement." WHERE bands.status = 'Active' ";	
				}else{
					$statement=$statement." AND bands.status = 'Active' ";	
				}
				
			}else if($_GET['estado']=="2"){
				if($arg==0){
					$arg++;
					$statement=$statement." WHERE bands.status = 'Split-up' ";	
				}else{
					$statement=$statement." AND bands.status = 'Split-up' ";	
				}
				
			}else if($_GET['estado']=="3"){
				if($arg==0){
					$arg++;
					$statement=$statement." WHERE bands.status = 'Changed name' ";	
				}else{
					$statement=$statement." AND bands.status = 'Changed name' ";	
				}
				
			}else if($_GET['estado']=="4"){
				if($arg==0){
					$arg++;
					$statement=$statement." WHERE bands.status = 'Unknown' ";	
				}else{
					$statement=$statement." AND bands.status = 'Unknown' ";	
				}
				
			}else if($_GET['estado']=="5"){
				if($arg==0){
					$arg++;
					$statement=$statement." WHERE bands.status = 'On hold' ";	
				}else{
					$statement=$statement." AND bands.status = 'On hold' ";	
				}
				
			}else if($_GET['estado']=="6"){
				if($arg==0){
					$arg++;
					$statement=$statement." WHERE bands.status = 'Disputed' ";	
				}else{
					$statement=$statement." AND bands.status = 'Disputed' ";	
				}
				
			}
		}
	}

	if(isset($_POST['pais'])){
		if($_POST['pais']!="0"){
			if($arg==0){
				$arg++;
				$statement=$statement." WHERE country.name = "."'".$_POST['pais']."' ";	
			}else{
				$statement=$statement." AND country.name = "."'".$_POST['pais']."' ";	
			}
			
		}
	}else{
		if(isset($_GET['pais'])){
			if($_GET['pais']!="0"){
				if($arg==0){
					$arg++;
					$statement=$statement." WHERE country.name = "."'".$_GET['pais']."' ";	
				}else{
					$statement=$statement." AND country.name = "."'".$_GET['pais']."' ";	
				}
				
			}
		}
	}

	$total=$statement." RETURN count(albums) as total";
	$total=$client->run(
		$total
	);

	$paginas=ceil($total[0]->get('total')/60);

        $inicio=0;

	if(isset($_GET['pagina'])){
	$inicio=$_GET['pagina']-1;	
	}
	$inicio=$inicio*60;

	$statement=$statement.' RETURN albums, bands, genres, country';

      	if(isset($_POST['orden'])){
		if($_POST['orden']==0){
			$statement=$statement.' ORDER BY albums.name';
		}else if($_POST['orden']==1){
			$statement=$statement.' ORDER BY albums.name DESC';
		}else{
			$statement=$statement.' ORDER BY albums.points DESC, albums.number';
		}
	}else{
		if(isset($_GET['orden'])){
			if($_GET['orden']==0){
				$statement=$statement.' ORDER BY albums.name';
			}else if($_GET['orden']==1){
				$statement=$statement.' ORDER BY albums.name DESC';
			}else{
				$statement=$statement.' ORDER BY albums.points DESC, albums.number';
			}
		}
	}

	$statement=$statement." SKIP ".$inicio." LIMIT 60 ";	

      $albums=$client->run(
		$statement
	);

	$genres=$client->run(
		'MATCH (genre:Genre)		
		 RETURN genre'
	);

	$countries=$client->run(
		'MATCH (country:Country)		
		 RETURN country'
	);

    }catch(Exception $e){
      die("Error: " . $e->GetMessage());
    }

?>

<ul class="topnav">

  <li><a href="profile.php">Perfil</a></li>
  <li><a class='active' href="selector.php">Selector</a></li>
  <li><a href="members.php">Miembros</a></li>
  <li><a href="random.php">Aleatorio</a></li>
  <li><a>Puntos: <?php foreach($result as $user){
						echo $user->get('user')->points;
					} ?></a></li>
  <li class="right"><a href="php/close.php">Cerrar sesión</a></li>
</ul>
  
    <h1>Selector</h1>
     
    <table width="80%" border="0" align="center">

      <tr>
        <td colspan="10" class="tableHeader"><form method="post" action=<?php echo $_SERVER["PHP_SELF"]; ?> > Orden: <?php

	echo '<select name="orden">
	   <option selected value="0"> A-Z </option>     
	   <option value="1"> Z-A </option> 
	   <option value="2"> Mejor </option> 
	   
	</select>'."\n";

	?>  Principal: <?php

	echo '<select name="genero">
	   <option selected value="0"> None </option>';
	   foreach($genres as $genre) {

		echo '<option value="'.$genre->get('genre')->name.'">'.$genre->get('genre')->name.'</option>'."\n\t";
	   }    
	   
	echo '</select>';

	?> Alt: <?php

	echo '<select name="generoSec">
	   <option selected value="0"> None </option>';
	   foreach($genres as $genre) {

		echo '<option value="'.$genre->get('genre')->name.'">'.$genre->get('genre')->name.'</option>'."\n\t";
	   }    
	   
	echo '</select>';

	?>  Estado: <?php

	echo '<select name="estado">
	   <option selected value="0"> None </option>;
	   <option value="1"> Active </option>     
	   <option value="2"> Split-up </option> 
	   <option value="3"> Changed name </option>
	   <option value="4"> Unknown </option>     
	   <option value="5"> On hold </option> 
	   <option value="6"> Disputed </option> '; 
	   
	echo '</select>';

	?> Pais: <?php

	echo '<select name="pais">
	   <option selected value="0"> None </option>';
	   foreach($countries as $country) {

		echo '<option value="'.$country->get('country')->name.'">'.$country->get('country')->name.'</option>'."\n\t";
	   }    
	   
	echo '</select>';

	?>
	<input type="submit" name="filtrar" id="filtrar" value="Filtrar"></form>
	</td>

      </tr>
      <tr>

      <?php foreach($albums as $album): if($row==4): $row=0;?>
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
	echo '<br> Estado: '.$album["bands"]->status;
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
			  $gets="";
					  
			if(isset($_POST['generoSec'])){
				$gets=$gets."&generoSec=".$_POST['generoSec'];
			}
			if(isset($_POST['genero'])){
				$gets=$gets."&genero=".$_POST['genero'];
			}
			if(isset($_POST['pais'])){
				$gets=$gets."&pais=".$_POST['pais'];
			}
			if(isset($_POST['orden'])){
				$gets=$gets."&orden=".$_POST['orden'];
			}
			if(isset($_POST['estado'])){
				$gets=$gets."&estado=".$_POST['estado'];
			}
			  if($i%40==0){
				echo '<br>';
			  }
			  echo '<a href="?pagina='.$i.$gets.'"> '.$i.' </a>';
			}?>
      </td></tr>

    </table>

</body>
</html>