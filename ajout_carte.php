<?php
	
	$ID_temporaire_acheteur = "30";

	$typecarte = isset($_POST["typecarte"])? $_POST["typecarte"] : ""; 
	$numero_carte = isset($_POST["numero_carte"])? $_POST["numero_carte"] : ""; 
	$titulaire_carte = isset($_POST["titulaire_carte"])? $_POST["titulaire_carte"] : ""; 
	$date_exp_carte = isset($_POST["date_exp_carte"])? $_POST["date_exp_carte"] : ""; 
	$mdp = isset($_POST["mdp"])? $_POST["mdp"] : ""; 

	$database = "ebay ece paris";
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);

    if($db_found){
    	if (isset($_POST["boutonajoutcarte"])) {
    		
    		echo "$typecarte<br>";
    		echo "$numero_carte<br>";
    		echo "$titulaire_carte<br>";
    		echo "$date_exp_carte<br>";
    		echo "$mdp<br>";
    		$sql = "UPDATE acheteur SET Type_carte = '$typecarte', Numero_carte = '$numero_carte', Nom_carte = '$titulaire_carte' , Date_exp_carte = '$date_exp_carte', Code_securite = '$mdp', Solde = '1000' WHERE ID = '$ID_temporaire_acheteur';";
    		$result = mysqli_query($db_handle, $sql);
    		echo "ouai";
    	}
    }else{
    	echo "base non trouvé";
    }
    
?>
