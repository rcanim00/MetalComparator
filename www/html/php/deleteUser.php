<?php



	require("../../checkAdmin.php");
	require("../../connection.php");

	if(!isset($_GET["Id"])){
		header("Location:../users.php");
	}

	try{

		$tsx=$client->beginTransaction();

		$tsx->run('MATCH (user:User) WHERE user.email = $email DELETE user',['email'=>$_GET["Id"]]);

		$tsx->commit();

		header("Location:../users.php");

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
