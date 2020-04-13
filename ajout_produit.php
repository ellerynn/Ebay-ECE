<?php
//vérifier si c'est bien un vendeur 
//extraire ID du fichier .txt pour enregistrer l'id dans la table item
//demander le nom, 
//les photos(autre table) [la partie photo à faire quand on aura fait tout les autres] //enregistre le nom de l'image
//Une vidéo(si optionnel: choix), la description, choix de catégorie, prix




//ajout photo

	 $nom = isset($_POST["nom"])? $_POST["nom"] : "";
	 $filephoto = isset($_POST["filephoto"])? $_POST["filephoto"] : "";
	 $videooption = isset($_POST["video"])? $_POST["video"] : ""; 
	 $fileVideo = isset($_POST["fileVideo"])? $_POST["fileVideo"] : "";
	 $description = isset($_POST["description"])? $_POST["description"]: "";
	 $categorie = isset($_POST["categorie"])? $_POST["categorie"] : "";
	 $prix = isset($_POST["prix"])? $_POST["prix"] : "";
	 $vente1 = isset($_POST["vente1"])? $_POST["vente1"] : "";
	 $vente2 = isset($_POST["vente2"])? $_POST["vente2"] : "";

	 $filenamevideo = "";
	

	 $erreur = "";

	 if ($nom == "") { 
	 	$erreur .= "Nom est vide. <br>"; }
 	 if ($videooption == "") { 
 		$erreur .= "Le choix de vouloir poster une vidéo ou non est vide. <br>"; }
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
	 	/*
	 	//photo
	 	if(isset($_POST['submitPhoto'])){
		 // Count total files
		 $countfiles = count($_FILES['filephoto']['name']);
		 
		 // Looping all files
		 for($i=0;$i<$countfiles;$i++){
		   $filename = $_FILES['filephoto']['name'][$i];
		   echo "nom de l'image : $filename";
		   // Upload file
		   move_uploaded_file($_FILES['filephoto']['tmp_name'][$i],'images_web/'.$filename);
		 }
		}
		*/

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
	                	//Vérification si le nom est déjà existant:
	                	$sql = "SELECT * FROM item WHERE Nom_item LIKE '$nom'";
	                	$result = mysqli_query($db_handle, $sql);
	                	if (mysqli_num_rows($result) != 0) 
	                    {
	                        echo "Un item de même nom est déjà existant.";
	                    }else{
	                    	//Video : 1 Cas à BLINDER 
			           		if ($videooption == "oui"){
				           		$countfiles = count($_FILES['filevideo']['name']);
								for($i=0;$i<$countfiles;$i++){
								   $filenamevideo = $_FILES['filevideo']['name'][$i];
								   move_uploaded_file($_FILES['filevideo']['tmp_name'][$i],'videos_web/'.$filenamevideo);
								}
							}
							
		                	$sql = "INSERT INTO item(ID_type_vente, Nom_item, Description, Categorie, Prix, Video) VALUES ('$type_vente_choisi','$nom','$description','$categorie','$prix','$filenamevideo');";
		                	$result = mysqli_query($db_handle, $sql);

		                	//Normalement c'est ajouté , mtn vérifions: 
		                	$sql = "SELECT * FROM item WHERE Nom_item LIKE '$nom'";
		                	$result = mysqli_query($db_handle, $sql);
		                	if (mysqli_num_rows($result) != 0) 
		                    {
		                        echo "Votre item a été ajouté avec succes";
		                    } 
							 // Count total files
							$countfiles = count($_FILES['filephoto']['name']);
							 // Looping all files
							for($i=0;$i<$countfiles;$i++){
							   $filenamephoto = $_FILES['filephoto']['name'][$i];
							   // Upload file
							   move_uploaded_file($_FILES['filephoto']['tmp_name'][$i],'images_web/'.$filenamephoto);
							   $sql = "INSERT INTO photo(Nom_photo, Nom_item) VALUES ('$filenamephoto','$nom');";
			                   $result = mysqli_query($db_handle, $sql);
							}
							
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