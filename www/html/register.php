<!DOCTYPE HTML>
<html> 
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Register</title>
            <link rel="shortcut icon" href="img/cards_icon.png" type="image/x-icon">
            <link rel="stylesheet" type="text/css" href="css/estilos.css"/>
        </head> 

        
<body id="fondo">
        
 <h1>
    Registrarse
</h1>


<?php 
    //Si boton enviar ha sido pulsado
    if(isset($_POST["enviar"])){

        require "../addUser.php";
        //Se comprueba que esten todos los datos del form
        if(isset($_POST["nombre"], $_POST["apellidos"], $_POST["fecha"], $_POST["email"], $_POST["contrasena"])){

            $email=$_POST["email"];
            //Se comprueba si existe usuario
            $type=check($email);
            //Se crea un hash de la password
            $crypt=password_hash($_POST["contrasena"],PASSWORD_DEFAULT);
            $name=$_POST["nombre"];
            $surnames=$_POST["apellidos"];
            $date=$_POST["fecha"];
            //si el retorno mayorque 0 inserta usuario, en caso contrario muestra informacion
            if($type>=0) {
                addUser($crypt,$type,$name,$surnames,$date,$email);
            }else {
                echo '<h2 style="text-align: center;"> Error: Existe otro usuario con el mismo email </h2>';
            }

        }else{
            echo '<h2 style="text-align: center;"> Error: Los campos requeridos no estan completos </h2>';
        }

    }

?>


<br><br>

<table  id="tabla" align="center" class="centered">
    
        
 <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="post">
    <tr>
    <td>
        <label style="color:#00708F;font-size: 150%;">Nombre:</label>
    </td>
    <td>
        <input type="text" name="nombre" required>
    </td>
    </tr>
  
    <tr>
    <td>
        <label style="color:#00708F;font-size: 150%;">Apellidos:</label>
    </td>
    <td>
        <input type="text" name="apellidos" required>
    </td>
    </tr>
  
    <tr>
    <td>
        <label style="color:#00708F;font-size: 150%;">F.de nacimiento:</label>
    </td>
    <td> 
        <input type="date" name="fecha"  required >
    </td>
    </tr>
 
    <tr>
    <td>
        <label style="color:#00708F;font-size: 150%;">Correo electrónico:</label> 
    </td>
    <td>
        <input type="text" name="email" required>
    </td>
    </tr>

    <tr>
    <td>
        <label style="color:#00708F;font-size: 150%;">Contraseña:</label> 
    </td>
    <td>
        <input type="password" name="contrasena" required>

    </td>
    </tr>
    <tr>
    <td colspan="2">
        <input align="center" type="submit" name="enviar" value="Enviar">
    </td>
    </tr>
     <tr>
    <td colspan="2">
        <a href="login.php"> ¿Ya estás registrado? Iniciar Sesión </a>
    </td>
    </tr>
</form>

</body>
</html>
