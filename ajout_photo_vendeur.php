<?php

	$ID_VENDEUR_TEMPORAIRE = "4";
	//FAUDRA VERIFIER QUI VEND AVEC LA CONNEXION AVANT D'AJOUTER à LA BDD L'item

	 $filephoto = isset($_POST["filephoto"])? $_POST["filephoto"] : "";
	 $filephotofond = isset($_POST["filephotofond"])? $_POST["filephotofond"] : "";



	$erreur ="";

    //identifier votre BDD
    $database = "ebay ece paris";

    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);


   if (isset($_POST["buttonmodifierphotoprofil"])) 
   {
   		//erreur condition pour image de profil 
		if ($_FILES['filephoto']['name'][0] =="") {
	 	 	$erreur .= "Aucune photo n'a été ajouté. <br>";
	 	}
	 	 
	 	if(count($_FILES['filephoto']['name']) != 1){
	 		$erreur .= "Vous devez choisir qu'une seule image de profil. <br>";
	 	}
	 	if (count($_FILES['filephoto']['name']) == 1 && $_FILES['filephoto']['type'][0] != "image/jpeg" && $_FILES['filephoto']['name'][0] != ""){
	 	 	$erreur .="L'image choisie doit être en .jpg. <br>";
	 	}

       	if ($erreur == "") 
		{
				 ///BDD
	            if ($db_found) 
	            {         
					echo "La photo de profil a bien été modifié";
            		$filename = $_FILES['filephoto']['name'][0];
                	$sql = "UPDATE vendeur SET ID_photo = '$filename' WHERE ID = $ID_VENDEUR_TEMPORAIRE;";
                	$result = mysqli_query($db_handle, $sql);   	
	             }
	             else 
	             {
	                  echo "Database not found";
	             }
	    }else{
	        	 echo "Erreur : <br>$erreur";
        }
         
   }


	           if (isset($_POST["buttonmodifierimagefond"])) 
	           {

	           		//erreur condition pour image de fond 
				 	if ($_FILES['filephotofond']['name'][0] =="") {
				 	 	$erreur .= "Aucune image de fond n'a été ajouté. <br>";
				 	}
				 	 
				 	if(count($_FILES['filephotofond']['name']) != 1){
				 		$erreur .= "Vous devez choisir qu'une seule image de fond. <br>";
				 	}
				 	if (count($_FILES['filephotofond']['name']) == 1 && $_FILES['filephotofond']['type'][0] != "image/jpeg" && $_FILES['filephotofond']['name'][0] != ""){
				 	 	$erreur .="L'image de fond choisie doit être en .jpg. <br>";
				 	}

				 	if($erreur == ""){
		                if ($db_found) 
		                {
		                    	
		              			$filename = $_FILES['filephotofond']['name'][0];
			                	$sql = "UPDATE vendeur SET ID_image_fond = '$filename' WHERE ID = $ID_VENDEUR_TEMPORAIRE;";
			                	$result = mysqli_query($db_handle, $sql);
			                	echo "L'image de fond a bien été modifié.";
		                 }
		                 else 
		                 {
		                      echo "Database not found";
		                 }
		             }else{
		             	echo "Erreur : <br>$erreur";
		             }
	                 
	           }     
	      //fermer la connexion
	      mysqli_close($db_handle);
	   
?>