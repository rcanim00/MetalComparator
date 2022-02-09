<?php



	require("../../checkAdmin.php");
	require("../../connection.php");

	if(!isset($_GET["Id"])){
		header("Location:../countries.php");
	}

	try{

		$tsx=$client->beginTransaction();

		$tsx->run('MATCH (country:Country)<-[:FROM_COUNTRY]-(:Band)<-[rel:FROM_ARTIST]-(a:Album) WHERE country.name = $name DELETE a, rel',['name'=>$_GET["Id"]]);

		$tsx->run('MATCH (country:Country)<-[rel:FROM_COUNTRY]-(b:Band) WHERE country.name = $name DELETE b, rel',['name'=>$_GET["Id"]]);		

		$tsx->run('MATCH (country:Country) WHERE country.name = $name DELETE country',['name'=>$_GET["Id"]]);

		$tsx->commit();

		header("Location:../countries.php");

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