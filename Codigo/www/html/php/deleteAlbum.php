<?php



	require("../../checkAdmin.php");
	require("../../connection.php");

	if(!isset($_GET["Id"])){
		header("Location:../albums.php");
	}

	try{

		$tsx=$client->beginTransaction();

		$tsx->run('MATCH (album:Album) WHERE album.name = $name DETACH DELETE album',['name'=>$_GET["Id"]]);

		$tsx->commit();

		header("Location:../albums.php");

	}catch(Exception $e){

		if (isset ($base)) { 
	    		$tsx->rollback();
	   		$tsx=null;
	    		echo '<h2 style="text-align: center;"> Error: ' . $e->GetMessage() . '</h2>'; 
		}else{
			$tsx=null;
			echo '<h2 style="text-align: center;"> Error: ' . $e->GetMessage() . '</h2>';
		}
	}

	?>
