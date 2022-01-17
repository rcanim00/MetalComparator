<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="img/cards_icon.png" type="image/x-icon">
<title>Perfíl</title>
<link rel="stylesheet" type="text/css" href="css/std.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

      $total=$client->run(
		'MATCH (user:User)
		 WHERE user.points > 0	
		 RETURN count(user) as total'
	);

      $paginas=ceil($total[0]->get('total')/100);

      $inicio=0;

      if(isset($_GET['pagina'])){
	$inicio=$_GET['pagina']-1;	
      }
      $inicio=$inicio*100;

      $users=$client->run(
		'MATCH (user:User)
		 WHERE user.points > 0	
		 RETURN user
		 SKIP '.$inicio.' LIMIT 100'
	);

    }catch(Exception $e){
      die("Error: " . $e->GetMessage());
    }

?>

<ul class="topnav">

  <li><a href="profile.php">Perfil</a></li>
  <li><a href="selector.php">Selector</a></li>
  <li><a class='active' href="members.php">Miembros</a></li>
  <li><a href="random.php">Aleatorio</a></li>
  <li><a>Puntos: <?php foreach($result as $user){
						echo $user->get('user')->points;
					} ?></a></li>
  <li class="right"><a href="php/close.php">Cerrar sesión</a></li>
</ul>
  
    <h1>Miembros</h1>
     
    <table width="50%" border="0" align="center">

      <tr>
        <td colspan="10" class="tableHeader"> Perfiles </td>

      </tr>
      <tr>

      <?php foreach($users as $user):?>
        

      <td>
	<a style="text-decoration: none" href="membersProfile.php?id=<?php echo $user["user"]->email; ?>"><?php echo 'Nombre: '.$user["user"]->name.' '.$user["user"]->surnames; ?>
	<?php echo ' Email: '.$user["user"]->email.' Fecha de nacimiento: '.$user["user"]->date; ?>
	<?php echo ' Puntos: '.$user["user"]->points; ?></a>
	
      </td>  
	</tr>
	<tr>
      <?php endforeach;?>
      </tr>
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