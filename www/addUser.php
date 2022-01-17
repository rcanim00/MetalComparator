<?php
	
	//Agnade el usuario a la base de datos
function addUser($crypt,$type,$name,$surnames,$date,$email) {
	require('connection.php');
   	try{

		$tsx = $client->beginTransaction();

		if($type==1){
			$tsx->run(
				'CREATE (:User:Admin {email: $email, name: $name, surnames: $surnames, date: $date, pass: $pass, points: $points})', 
				array('email'=>$email,'name'=>$name,'surnames'=>$surnames,'date'=>$date,'pass'=>$crypt,'points'=>0)
			);
		}else{
			$tsx->run(
				'CREATE (:User {email: $email, name: $name, surnames: $surnames, date: $date, pass: $pass, points: $points})', 
				array('email'=>$email,'name'=>$name,'surnames'=>$surnames,'date'=>$date,'pass'=>$crypt,'points'=>0)
			);
		}
		
		$tsx->commit();

		echo '<h2 style="text-align: center;"> El usuario se ha introducido con exito. </h2>';

	}catch(Exception $e){

		if (isset ($tsx)) { 
	    		$tsx->rollback();
	   		$client=null;
	    		echo '<h2 style="text-align: center;"> Error: ' . $e->GetMessage() . '</h2>'; 
    		}else{
			echo '<h2 style="text-align: center;"> Error: ' . $e->GetMessage() . '</h2>';
		}
	}

}

	//Comprueba si el usuario puede ser introducido en la base de datos, y con que rol
function check($email) {
	require('connection.php');
	try{

		$result=$client->run(
			'MATCH (users:User) WHERE users.email = $email RETURN count(users) AS total', ['email'=>$email]
		);

		//Si existe otro usuario con el mismo email devuelve -1
		
		if($result[0]->get('total')>0) {
			$client=null;				
			return -1;				
		}

		
		$result=$client->run(
		    'MATCH (users:Admin) RETURN count(users) AS total'
		);
		//Devuelve 1 si no existe ningun usuario, marcando que tendra rol admin, en caso contrario 0, marcando usuario estandar
		if($result[0]->get('total')==0) {
			$client=null;
			return 1;
		}else{
			$client=null;
			return 0;
		}

		

	}catch(Exception $e){

		if (isset ($client)) {
	   		$client=null;
	    		echo '<h2 style="text-align: center;"> Error: ' . $e->GetMessage() . '</h2>'; 
		}else{
			echo '<h2 style="text-align: center;"> Error: ' . $e->GetMessage() . '</h2>';
		}	

		
	}
		
}


?>