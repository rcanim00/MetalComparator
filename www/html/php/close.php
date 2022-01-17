<?php
	//Destruye las sesiones activas, y redirige a login
	session_start();
	session_unset(); 
    session_destroy();
    header("Location:/login.php");

?>