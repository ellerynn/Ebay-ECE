<?php

	$ID_VENDEUR_TEMPORAIRE = "12";
	//FAUDRA VERIFIER QUI VEND AVEC LA CONNEXION AVANT D'AJOUTER à LA BDD L'item

	 $nom = isset($_POST["nom"])? $_POST["nom"] : "";
	 $filephoto = isset($_POST["filephoto"])? $_POST["filephoto"] : "";
	 $videooption = isset($_POST["video"])? $_POST["video"] : ""; 
	 $filevideo = isset($_POST["fileVideo"])? $_POST["fileVideo"] : "";
	 $description = isset($_POST["description"])? $_POST["description"]: "";
	 $categorie = isset($_POST["categorie"])? $_POST["categorie"] : "";
	 $prix = isset($_POST["prix"])? $_POST["prix"] : "";
	 $vente1 = isset($_POST["vente1"])? $_POST["vente1"] : "";
	 $vente2 = isset($_POST["vente2"])? $_POST["vente2"] : "";


	 $erreur ="";
	 if ($nom == "") { 
	 	$erreur .= "Nom est vide. <br>"; }
	 if ($_FILES['filephoto']['name'][0] =="") {
 	 	$erreur .= "Aucune photo n'a été ajouté. <br>";
 	 }
 	 //verification de type des photos :
	$countfiles = count($_FILES['filephoto']['name']);
	for($i=0;$i<$countfiles;$i++){
		if ($_FILES['filephoto']['type'][$i] != "image/jpeg" && $_FILES['filephoto']['name'][0] !=""){
			$erreur .= "Une ou plusieurs images ne sont pas en .jpg. <br>";
		}
	}//fin de la vérification
 	 if ($videooption == "") {
 	 	$erreur .= "L'option ajouté une video ou non est vide. <br>";
 	 }
 	 
 	 if($videooption == "oui" && $_FILES['filevideo']['name'][0] ==""){
 	 	$erreur .= "Vous avez choisi de poster une vidéo mais aucune vidéo n'a été postée. <br>";
 	 }
 	 //choix non mais avec une vidéo
 	 if($videooption == "non" && $_FILES['filevideo']['name'][0] != ""){
 	 	$erreur .= "Option vidéo 'non' pourtant une vidéo est séléctioné. <br>";
 	 	//header('Location: http://localhost/projet/Ebay-ECE/vendre.html');
 	 }
 	 if($videooption == "oui" && count($_FILES['filevideo']['name']) != 1){
 	 	$erreur .= "Vous devez choisir qu'une seule vidéo. <br>";
 	 }
 	 //vérification du type de la vidéo
 	 if ($videooption == "oui" && count($_FILES['filevideo']['name']) == 1 && $_FILES['filevideo']['type'][0] != "video/mp4" && $_FILES['filevideo']['name'][0] != ""){
 	 	$erreur .="La vidéo choisi doit être en .mp4. <br>";
 	 }
	 if ($description == "") { 
	 	$erreur .= "La description est vide. <br>"; }
	 if ($categorie == "") {
	 	$erreur .= "La catégorie est vide. <br>"; }
	 if ($prix == "") {
	 	$erreur .= "Le prix est vide. <br>"; }
	 if ($vente1 == "" && $vente2 ==""){
	 	$erreur .= "Le choix de vente est vide. <br>";
	 }
	 if ($erreur == "") 
	 {
	 	$type_vente_choisi = $vente1." ". $vente2;
	     //identifier votre BDD
	     $database = "ebay ece paris";

	     $db_handle = mysqli_connect('localhost', 'root', '');
	     $db_found = mysqli_select_db($db_handle, $database);
	           if (isset($_POST["boutonajoutproduit"])) 
	           {
					 ///BDD
	                if ($db_found) 
	                {
	                    	//Video : 1 Cas à BLINDER PAR Style d'HTML
	                		$filenamevideo = "";
			           		if ($videooption == "oui"){
				           		$countfiles = count($_FILES['filevideo']['name']);
								for($i=0;$i<$countfiles;$i++){
								   $filenamevideo = $_FILES['filevideo']['name'][$i];
								   move_uploaded_file($_FILES['filevideo']['tmp_name'][$i],'videos_web/'.$filenamevideo);
								}
							}
							
		                	$sql = "INSERT INTO item(ID_vendeur, ID_type_vente, Nom_item, Description, Categorie, Prix, Video) VALUES ($ID_VENDEUR_TEMPORAIRE,'$type_vente_choisi','$nom','$description','$categorie','$prix','$filenamevideo');";
		                	$result = mysqli_query($db_handle, $sql);
		                	//Normalement c'est ajouté , mtn vérifions et extraction de l'ID: 
		                	$sql = "SELECT LAST_INSERT_ID(ID_item) FROM item ";
		                	$result = mysqli_query($db_handle, $sql);

		                	$last_id_item = "";
		                	if (mysqli_num_rows($result) != 0)
		                    {
		                        echo "Votre item a été ajouté avec succes";
		                        while ($data = mysqli_fetch_assoc($result)) 
                              {
                                $last_id_item = $data['LAST_INSERT_ID(ID_item)'];
                              }
		                    } 

							 // Count total files
							$countfiles = count($_FILES['filephoto']['name']);
							 // Looping all files
							for($i=0;$i<$countfiles;$i++){
							   $filenamephoto = $_FILES['filephoto']['name'][$i];
							   // Upload file
							   move_uploaded_file($_FILES['filephoto']['tmp_name'][$i],'images_web/'.$filenamephoto);
							   $sql = "INSERT INTO photo(Nom_photo, ID_item) VALUES ('$filenamephoto','$last_id_item');";
			                   $result = mysqli_query($db_handle, $sql);
							}
	                 }
	                 else 
	                 {
	                      echo "Database not found";
	                 }
	                 
	           }   
	      //fermer la connexion
	      mysqli_close($db_handle);
	   }  
	   else 
	   {
	      echo "Erreur : <br>$erreur";
	   }
	   
?>