<!DOCTYPE html>

<html> 

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="shortcut icon" href="img/cards_icon.png" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/estilos.css"/>
</head> 

<body id="fondo">
             
         <h1> 
            Iniciar Sesión
        </h1>

        <br><br>

        <?php

            session_start();
            //Si hay sesiones validas se redirige al crud(admin) , o a profile(usuario estandar)
            if(isset($_SESSION["renovation"]) && (time()-$_SESSION["renovation"]>1800)) {
                session_regenerate_id(true);
                $_SESSION["renovation"]=time(); 
            }

            if (isset($_SESSION["timeout"]) && (time()-$_SESSION["timeout"]>1800)) {
                session_unset(); 
                session_destroy();
                header("Location:login.php");
            }else if(isset($_SESSION["timeout"])){
                 $_SESSION["timeout"]=time();
            }

            if(isset($_SESSION["admin"])) {
                header("Location:users.php");
            }else if(isset($_SESSION["user"])){
                header("Location:profile.php");
            }
            //Si se ha pulsado el boton enviar se comprueba el usuario y la password
            if(isset($_POST["enviar"])){

                require "../checkUser.php";

                if(isset($_POST["usuario"], $_POST["contrasena"])) {

                    $identifier=$_POST["usuario"];
                    $password=$_POST["contrasena"];

                    $ret=checkUser($identifier,$password);
                    //Si ret==0 entonces usuario estandar, se crean sesiones y redirige, con ret==1 lo mismo con admin
                    //Si ret==-1 no existe usuario con ese identificador, si ret==-2 la password es erronea
                    if($ret==0) {
                        $_SESSION["user"]=$identifier;
                        $_SESSION["timeout"]=time();
                        $_SESSION["renovation"]=time();
                        header("Location:profile.php");
                    }else if($ret==1) {
                         $_SESSION["admin"]=$identifier;
                         $_SESSION["timeout"]=time();
                         $_SESSION["renovation"]=time();
                        header("Location:users.php");
                        
                    }else if($ret==-1) {
                        $_POST["temp"]=1;
                    }else if($ret==-2){
                        $_POST["temp"]=0;
                    }

                }else{
                    echo '<h2 style="text-align: center;"> Error: Los campos requeridos no estan completos </h2>';
                }

            }

        ?>
        
            <table align="center" class="centered"> 
            <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="POST">
            <tr>
            <td>
                <label style="color:#00708F;font-size: 150%;">Usuario:</label> 
            </td>
            <td>
                <input type="text" placeholder="Ingrese el identificador" name="usuario" required>
            </td>
            </tr>

            <tr>
            <td>
                <label style="color:#00708F;font-size: 150%;">Contraseña:</label> 
            </td>
            <td>
            
                <input type="password" placeholder="Ingrese la contraseña" name="contrasena" required>
            </td>
            </tr>
 
            <tr>
             <td colspan="2">
            <input align="center" type="submit" name="enviar" value="Enviar"> <br><br>
            </td>   
            </tr>
            <tr>
            <td colspan="2">
            <a href="register.php"> ¿No tienes cuenta? Registrarse ahora </a>
            </td>
            </tr>
            </form>
            </table>
    

        <?php
            //muestra mensaje
            if(isset($_POST["temp"])){
                if($_POST["temp"]==1){
                    echo '<h2 id="fail"> El usuario introducido no existe </h2>';
                }else{
                    echo '<h2 id="fail"> La contraseña introducida es incorrecta </h2>';
                }
            }
        ?>

</body>

</html>
