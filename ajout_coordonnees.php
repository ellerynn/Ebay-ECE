<?php
	//////A MODIFIER DANS LA BDD le Code Postal en MAJJ !!

	$ID_temporaire_acheteur = "30";

	$adresseUn = isset($_POST["adresseUn"])? $_POST["adresseUn"] : ""; 
	$adresseDeux = isset($_POST["adresseDeux"])? $_POST["adresseDeux"] : ""; 
	$ville = isset($_POST["ville"])? $_POST["ville"] : ""; 
	$codePostal = isset($_POST["codePostal"])? $_POST["codePostal"] : ""; 
    $pays = isset($_POST["pays"])? $_POST["pays"] : ""; 
    $Telephone = isset($_POST["telephone"])? $_POST["telephone"] : ""; 

	$database = "ebay ece paris";
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);

    if($db_found){
    	if (isset($_POST["bontonaddcoords"])) {
    		echo "hello";
    		echo "$adresseUn<br>";
    		echo "$adresseDeux<br>";
    		echo "$ville<br>";
    		echo "$codePostal<br>";
            echo "$pays<br>";
    		echo "$Telephone<br>";
    		$sql = "UPDATE acheteur SET Adresse_ligne1 = '$adresseUn', Adresse_ligne2 = '$adresseDeux', Ville = '$ville' , Code_postal = '$codePostal', Pays = '$pays', Telephone = '$Telephone' WHERE ID = '$ID_temporaire_acheteur';";
    		$result = mysqli_query($db_handle, $sql);
    		echo "ouai";
    	}
    }else{
    	echo "base non trouvé";
    }
    
?>
