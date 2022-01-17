<?php
	//Comprueba si el usuario se encuentra en la base de datos, y si coincide la password pasada
	function checkUser($identifier,$password) {
	    require('connection.php');
	    try{

		$result=$client->run(
			'MATCH (users:Admin) WHERE users.email = $email RETURN users', ['email'=>$identifier]
		);

		//Si existe otro usuario con el mismo email devuelve -1

		$type=-1;

		/*foreach ($results as $result){

			$node=$result->get('node');
			echo $node->getAttribute('id');		
			
		}*/

		foreach ($result as $node){	
			$type=1;
		}

		if($type!=1) {

			$result=$client->run(
				'MATCH (users:User) WHERE users.email = $email RETURN users', ['email'=>$identifier]
			);

			foreach ($result as $node){	
				$type=0;
			}
			
			if($type==-1) {		
				return $type;				
			}			
							
		}

		//si las password coinciden, devuelve 0 si se trata de un usuario estandar, y 1 si es un admin, en caso contrario devuelve -2
		if(password_verify($password,trim($result[0]->get('users')->pass))) {
			if($type==0){
				return 0;
			}else{
				return 1;
			}
		}else {
			return -2;
		}

	}catch(Exception $e){
		//Si hay error devuelve -3
		if (isset ($client)) { 
		   	$client=null;
		    	echo 'Error: ' . $e->GetMessage(); 
		    	return -3;
		}else{

			echo 'Error: ' . $e->GetMessage();
			return -3;

		}
	}

}

?>