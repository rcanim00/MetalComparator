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

<h1>Actualizar Usuario</h1>


<?php
  

if(!isset($_POST["update"])) {

	if(!isset($_GET["type"],$_GET["name"],$_GET["surnames"],$_GET["bday"],$_GET["points"],$_GET["email"])){ 
		header("Location:/users.php");
	}

	$type=$_GET["type"];
	$name=$_GET["name"];
	$surnames=$_GET["surnames"];
	$bday=$_GET["bday"];
	$points=$_GET["points"];
	$email=$_GET["email"];

}else{

	if(isset($_POST["type"])&&$_POST["type"]=="on"){
		$newType=1;
	}else{
		$newType=0;
	}
		$name=$_POST["name"]; //Almacena el valor del name 
		$surnames=$_POST["surnames"];
		$bday=$_POST["bday"];
		$points=$_POST["points"];
		$new=$_POST["email"];
		$email=$_POST["id"];

    try{

      $tsx=$client->beginTransaction();

      //Si la contraseña es distinta a nula se actualiza los valores y almacena una contraseña default 
      if(isset($_POST["password"]) && $_POST["password"]!=""){

        $password=password_hash($_POST["password"],PASSWORD_DEFAULT);
	
	if($newType==1){
		$tsx->run(
			'MATCH(user:User) 
			WHERE user.email = '."'".$email."'".'
			SET user += {
				name: $name, surnames: $surnames, email: $email,
				points: $points, date: $date, pass: $pass
			}
			SET user:Admin', ['name'=>$name,'surnames'=>$surnames,'pass'=>$password,'email'=>$new,'date'=>$bday,'points'=>$points]
		);
	}else{

		$tsx->run(
			'MATCH(user:User) 
			WHERE user.email = '."'".$email."'".'
			SET user += {
				name: $name, surnames: $surnames, email: $email,
				points: $points, date: $date, pass: $pass
			}
			REMOVE user:Admin', ['name'=>$name,'surnames'=>$surnames,'pass'=>$password,'email'=>$new,'date'=>$bday,'points'=>$points]
		);
	}

      }else{

	if($newType==1){
		$tsx->run(
			'MATCH(user:User) 
			WHERE user.email = '."'".$email."'".'
			SET user += {
				name: $name, surnames: $surnames, email: $email,
				points: $points, date: $date
			}
			SET user:Admin', ['name'=>$name,'surnames'=>$surnames,'email'=>$new,'date'=>$bday,'points'=>$points]
		);
	}else{
		$tsx->run(
			'MATCH(user:User) 
			WHERE user.email = '."'".$email."'".'
			SET user += {
				name: $name, surnames: $surnames, email: $email,
				points: $points, date: $date
			}
			REMOVE user:Admin', ['name'=>$name,'surnames'=>$surnames,'email'=>$new,'date'=>$bday,'points'=>$points]
		);
	}
     
      }

      $tsx->commit();

    }catch(Exception $e){

      if (isset($tsx)) { 
          $tsx->rollback();
          $tsx=null;
      }
      
    }

    header("Location:/users.php");

  }
  
?>


<form method="post" action=<?php echo $_SERVER["PHP_SELF"]; ?>>

  <input type="hidden" name="id" id="id" value="<?php echo $email; ?>">

  <table width="50%" border="0" align="center">
    
    <tr>
      <td>Contraseña</td>
      <td><label for="password"></label>
      <input type="password" name="password" id="password"></td>
    </tr>
    <tr>
      <td>Administrador</td>
      <td><label for="type"></label>
      <input type="checkbox" name="type" id="type" <?php if($type==1){ echo "checked"; } ?> ></td>
    </tr>
    <tr>
      <td>Nombre</td>
      <td><label for="name"></label>
      <input type="text" name="name" id="name" value="<?php echo $name; ?>" required></td>
    </tr>
    <tr>
      <td>Apellidos</td>
      <td><label for="surnames"></label>
      <input type="text" name="surnames" id="surnames" value="<?php echo $surnames; ?>" required></td>
    </tr>
    <tr>
      <td>Fecha de nacimiento</td>
      <td><label for="bday"></label>
      <input type="date" name="bday" id="bday" value="<?php echo $bday; ?>" required></td>
    </tr>
    <tr>
      <td>Puntos</td>
      <td><label for="points"></label>
      <input type="number" name="points" id="points" min="0" max="999999" value="<?php echo $points; ?>" required></td>
    </tr>
    <tr>
      <td>Email</td>
      <td><label for="email"></label>
      <input type="text" name="email" id="email" value="<?php echo $email; ?>" required></td>
    </tr>

    <tr>
      <td colspan="2"><input type="submit" name="update" id="update" value="Actualizar"></td>
    </tr>
  </table>
</form>

</body>
</html>
