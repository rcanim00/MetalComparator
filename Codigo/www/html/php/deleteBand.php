<?php



	require("../../checkAdmin.php");
	require("../../connection.php");

	if(!isset($_GET["Id"])){
		header("Location:../bands.php");
	}

	try{

		$tsx=$client->beginTransaction();

		$tsx->run('MATCH (a:Album)-[rel:FROM_ARTIST]->(band:Band) WHERE band.name = $name DELETE a, rel',['name'=>$_GET["Id"]]);

		$tsx->run('MATCH (band:Band) WHERE band.name = $name DELETE band',['name'=>$_GET["Id"]]);

		$tsx->commit();

		header("Location:../bands.php");

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