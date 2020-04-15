<?php

	$ID_VENDEUR_TEMPORAIRE = "12";
	//FAUDRA VERIFIER QUI VEND AVEC LA CONNEXION AVANT D'AJOUTER à LA BDD L'item
	$datetime = date('Y-m-d');
	echo $datetime;
	echo date('H:i');

	 $nom = isset($_POST["nom"])? $_POST["nom"] : "";
	 $filephoto = isset($_POST["filephoto"])? $_POST["filephoto"] : "";
	 $videooption = isset($_POST["video"])? $_POST["video"] : ""; 
	 $filevideo = isset($_POST["fileVideo"])? $_POST["fileVideo"] : "";
	 $description = isset($_POST["description"])? $_POST["description"]: "";
	 $categorie = isset($_POST["categorie"])? $_POST["categorie"] : "";
	 $prix = isset($_POST["prix"])? $_POST["prix"] : "";
	 $vente1 = isset($_POST["vente1"])? $_POST["vente1"] : ""; //achat immediat
	 $vente2 = isset($_POST["vente2"])? $_POST["vente2"] : ""; //enchere ou offres
	 $datedebut = isset($_POST["datedebut"])? $_POST["datedebut"] : "";
	 $heuredebut = isset($_POST["heuredebut"])? $_POST["heuredebut"] : "";
	 $datefin = isset($_POST["datefin"])? $_POST["datefin"] : "";
	 $heurefin = isset($_POST["heurefin"])? $_POST["heurefin"] : "";
	 $prixdepart = isset($_POST["prixdepart"])? $_POST["prixdepart"] : "";

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
	 if (($prix == "" && $vente1 != "") || ($prix == "" && $vente1 != "" && $vente2 == "offre") || ($prix == "" && $vente2 == "offre")) {
	 	$erreur .= "Le prix est vide. <br>"; }
	 if ($prix != "" && $vente2 == "enchere" && $prixdepart != "" && $prix<$prixdepart)
	 	$erreur .= "Le prix normal doit être supérieur au prix de départ pour l'enchere. <br>";
	 if ($vente1 == "" && $vente2 ==""){
	 	$erreur .= "Le choix de vente est vide. <br>";
	 }
	 if ($vente2 == "enchere"){
	 	if ($datedebut == "")
	 		$erreur .= "Vous n'avez pas choisi une date de début pour l'enchère du produit. <br>";
	 	if ($heuredebut == "")
	 		$erreur .= "Vous n'avez pas choisi une heure de début pour l'enchère du produit. <br>";
	 	if ($datefin == "")
	 		$erreur .= "Vous n'avez pas choisi une date de fin pour l'enchère du produit. <br>";
	 	if ($heurefin == "")
	 		$erreur .= "Vous n'avez pas choisi une heure de fin pour l'enchère du produit. <br>";
	 }
	 if ($prixdepart == "" && $vente2 == "enchere") {
 	 	$erreur .= "Le prix de départ pour l'enchère est vide. <br>";
 	 }
 	 if ($prixdepart >= $prix && $prixdepart != "" && $prix != "" && $vente2 == "enchere"){
 	 	$erreur .= "le prix pour l'enchère doit être inférieur au prix normal du produit. <br>";
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
							//s'il ne s'agit que d'un enchère Sinon le prix reste en prix par défaut: 
							if (strlen($type_vente_choisi) == 8)
								$prix = $prixdepart;
							
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

		                    //Ajout dans la table photo
							$countfiles = count($_FILES['filephoto']['name']);
							for($i=0;$i<$countfiles;$i++){
							   $filenamephoto = $_FILES['filephoto']['name'][$i];
							   move_uploaded_file($_FILES['filephoto']['tmp_name'][$i],'images_web/'.$filenamephoto);
							   $sql = "INSERT INTO photo(Nom_photo, ID_item) VALUES ('$filenamephoto','$last_id_item');";
			                   $result = mysqli_query($db_handle, $sql);
							}
							//Ajout dans la liste d'enchere si enchere.
							if (strlen($type_vente_choisi) == 8 || strlen($type_vente_choisi) == 22){
								echo "je suis dedans";
								$heuredebut .=":00";
								$heurefin .=":00";
								echo "$last_id_item"."<br>";
								echo "$datedebut"."<br>";
								echo "$heuredebut"."<br>";
								echo "$datefin"."<br>";
								echo "$heurefin"."<br>";
								echo "$prixdepart"."<br>";
								$sql = "INSERT INTO liste_enchere(ID_item, Date_debut, Heure_debut, Date_fin, Heure_fin, Prix) VALUES ('$last_id_item', '$datedebut', '$heuredebut', '$datefin', '$heurefin', '$prixdepart');";
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