<?php



	require("../../checkAdmin.php");
	require("../../connection.php");

	if(!isset($_GET["Id"])){
		header("Location:../genres.php");
	}

	try{

		$tsx=$client->beginTransaction();

		$tsx->run('MATCH (genre:genre)<-[rel:GENRE_IS]-(a:Album) WHERE genre.name = $name DELETE a, rel',['name'=>$_GET["Id"]]);		

		$tsx->run('MATCH (genre:Genre) WHERE genre.name = $name DELETE genre',['name'=>$_GET["Id"]]);

		$tsx->commit();

		header("Location:../genres.php");

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