<?php
	//Inicia las sesiones
	session_start();
	//Si ha pasado mas de 30 min, se destruyen las sesiones y redirige al login
	if (isset($_SESSION["timeout"]) && (time()-$_SESSION["timeout"]>1800)) {
		session_unset(); 
		session_destroy();
		header("Location:/login.php");
	} 
	//Si ha pasado mas de 30 minutos desde la ultima renovacion del identificador de sesion se renueva
	if (!isset($_SESSION["renovation"])) { 
		$_SESSION["renovation"]=time(); 
	}else if(time()-$_SESSION["renovation"]>1800) { 
		session_regenerate_id(true);
		$_SESSION["renovation"]=time(); 
	}
	//Si no existe una sesion de administrador se destruyen las sesiones, y se redirige al login
	if (!isset($_SESSION["admin"])) {
		session_unset(); 
		session_destroy();
		header("Location:/login.php");
	}   
	//Se actualiza el timestamp que marca la duracion de la sesion
	$_SESSION["timeout"]=time();

?>