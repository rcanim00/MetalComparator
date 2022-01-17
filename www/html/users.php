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

    $email=$_POST["email"];

    if(isset($_POST["type"])&&$_POST["type"]=="on"){
      $type=1;
    }else{
      $type=0;
    }

    $crypt=password_hash($_POST["password"],PASSWORD_DEFAULT);
    $name=$_POST["name"]; //Almacena el valor del name 
    $surnames=$_POST["surnames"];
    $date=$_POST["bday"];
    $points=$_POST["points"];

    try{

      $tsx = $client->beginTransaction();

	if($type==1){
		$tsx->run(
			'CREATE (:User:Admin {email: $email, name: $name, surnames: $surnames, date: $date, pass: $pass, points: $points})', 
			array('email'=>$email,'name'=>$name,'surnames'=>$surnames,'date'=>$date,'pass'=>$crypt,'points'=>0)
		);
	}else{
		$tsx->run(
			'CREATE (:User {email: $email, name: $name, surnames: $surnames, date: $date, pass: $pass, points: $points})', 
			array('email'=>$email,'name'=>$name,'surnames'=>$surnames,'date'=>$date,'pass'=>$crypt,'points'=>0)
		);
	}

	$tsx->commit();


    }catch(Exception $e){

      if (isset($tsx)) { 
          $tsx->rollback();
          $tsx=null;
      }
      
    }
    header("Location:users.php");
  }

?>

<ul class="topnav">
  <li><a class="active" href="users.php">Usuarios</a></li>
  <li><a href="countries.php">Paises</a></li>
  <li><a href="genres.php">Géneros</a></li>
  <li><a href="bands.php">Bandas</a></li>
  <li><a href="albums.php">Albums</a></li>
  <li class="right"><a href="php/close.php">Cerrar sesión</a></li>
</ul>

<h1>Usuarios</h1>
  
  <table width="50%" border="0" align="center">
    <tr >
      <td class="tableHeader">Contraseña</td>
      <td class="tableHeader">Permisos</td>
      <td class="tableHeader">Nombre</td>
      <td class="tableHeader">Apellidos</td>
      <td class="tableHeader">Fecha de nacimiento</td>
      <td class="tableHeader">Puntos</td>
      <td class="tableHeader">Email</td>
      <td class="invisible">&nbsp;</td>
      <td class="invisible">&nbsp;</td>
    </tr> 
   
		<?php 

    try{
      
      $total=$client->run(
		'MATCH (user:User)	
		 RETURN count(user) as total'
	);

      $paginas=ceil($total[0]->get('total')/100);

      $inicio=0;

      if(isset($_GET['pagina'])){
	$inicio=$_GET['pagina']-1;	
      }
      $inicio=$inicio*100;

      $result=$client->run(
		'MATCH (users:User) 
		RETURN users
		SKIP '.$inicio.' LIMIT 100'
	);

    }catch(Exception $e){
      die("Error: " . $e->GetMessage());
    }

    foreach($result as $user):?>
    

    <tr>
      <td> <?php echo $user->get('users')->pass; ?> </td>
      <td> <?php 
	$labels=$user->get('users')->getLabels();
	$type=-1;
	foreach($labels as $label){
		$type++;
	}
	if($type==1){
		echo 'Admin';
	}else{
		echo 'Estandar';
	}

      ?> </td>
      <td> <?php echo $user->get('users')->name; ?> </td>
      <td> <?php echo $user->get('users')->surnames; ?> </td>
      <td> <?php echo $user->get('users')->date; ?> </td>
      <td> <?php echo $user->get('users')->points; ?> </td>
      <td> <?php echo $user->get('users')->email; ?> </td>
 
      <td class="button"><a href="php/deleteUser.php?Id=<?php echo $user->get('users')->email; ?>"><input type="image" src="img/trash.png" width="25" height="25" name="delete" id="delete" value="Borrar"></a></td>

      <td class="button"><a href="php/updateUser.php?type=<?php echo $type; ?>&name=<?php echo $user->get('users')->name; ?>&surnames=<?php echo $user->get('users')->surnames; ?>&bday=<?php echo $user->get('users')->date; ?>&points=<?php echo $user->get('users')->points; ?>&email=<?php echo $user->get('users')->email; ?>"><input type="image" src="img/update.png"  width="25" height="25" name="update" id="update" value="Actualizar"></a></td>
    </tr>    

    <?php
    endforeach;
    ?>

      
	<tr>

      <form method="post" action=<?php echo $_SERVER["PHP_SELF"]; ?> >
      <td><input type="password" name="password" size="10" class="centrado" required></td>
      <td><input type="checkbox" name="type" size="10" class="centrado" ></td>
      <td><input type="text" name="name" size="10" class="centered" required></td>
      <td><input type="text" name="surnames" size="10" class="centered" required></td>
      <td><input type="date" name="bday" size="10" class="centered" required></td>
      <td><input type="number" name="points" size="10" min="0" max="999999" class="centered" required></td>
      <td><input type="text" name="email" size="10" class="centered" required></td>
      <td class="button"><input class="addButton" type="submit" name="add" id="add" value=""></td></tr>    
      </form>
      <tr><td colspan="7"> 
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
