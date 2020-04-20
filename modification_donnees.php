<?php
	//IMPORTANT A SAVOIR $id est issus de Session, ce fichier a pour but d'être inclu
	//Fichier en relation avec votre_compte.php et paiement.php

	 $filephoto = isset($_POST["filephoto"])? $_POST["filephoto"] : "";
	 $filephotofond = isset($_POST["filephotofond"])? $_POST["filephotofond"] : "";

	$erreur ="";

    //BDD
    $database = "ebay ece paris";
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);
	if ($db_found)
	{
	    ///DEBUT : Si modification de données des infos générals:
		if (isset($_POST["modifierGen"]))
		{	//Récup donnée
			$nomGen = isset($_POST["nomGen"])? $_POST["nomGen"] : ""; 
			$prenomGen = isset($_POST["prenomGen"])? $_POST["prenomGen"] : ""; 
			$emailGen = isset($_POST["emailGen"])? $_POST["emailGen"] : ""; 
			$mdpGen = isset($_POST["mdpGen"])? $_POST["mdpGen"] : ""; 
			if($nomGen != "")
			{	//Si le nom n'est pas vide, on maj le nom de la table personne
				$sql = "UPDATE personne SET Nom = '$nomGen' WHERE ID = '$id'";
				$result = mysqli_query($db_handle, $sql);
			}
			if($prenomGen != "")
			{	//Si le prénom n'est pas vide, on maj le prenom de la table personne 
				$sql = "UPDATE personne SET Prenom = '$prenomGen' WHERE ID = '$id'";
				$result = mysqli_query($db_handle, $sql);
			}
			if($emailGen != "")
			{	//Vérification de l'unicité
				$sql = "SELECT Email FROM personne WHERE Email = '$emailGen'";
				$result = mysqli_query($db_handle, $sql);
				if (mysqli_num_rows($result) == 0) 
				{	// dans le cas où le mail n'existe pas et donc unique on peut modif
					$sql = "UPDATE personne SET Email = '$emailGen' WHERE ID = '$id'";
					$result = mysqli_query($db_handle, $sql);
				}
			}
			if($mdpGen != "")
			{	//Si mdp n'est pas vide, on maj le mdp dans personne
				$sql = "UPDATE personne SET Mot_de_passe = '$mdpGen' WHERE ID = '$id'";
				$result = mysqli_query($db_handle, $sql);
			}
		}
		///FIN : Si modification de données des infos générals:

		///DEBUT : si modification données vendeurs:
		///Premier bouton : modifier la photo 
	   if (isset($_POST["buttonmodifierphotoprofil"])) 
	   {
	   		//erreur condition pour image de profil 
			if ($_FILES['filephoto']['name'][0] =="") //Si aucun fichier détécté à la pos 0
			{
		 	 	$erreur .= "Aucune photo n'a été ajouté. <br>";
		 	 	$_SESSION['Erreur'] = $erreur;
		 	}
		 	if(count($_FILES['filephoto']['name']) != 1){ //Si deuxième fichier détécté
		 		$erreur .= "Vous devez choisir qu'une seule image de profil. <br>";
		 	}
		 	if (count($_FILES['filephoto']['name']) == 1 && $_FILES['filephoto']['type'][0] != "image/jpeg" && $_FILES['filephoto']['name'][0] != "")
		 	{	//Si format non désiré
		 	 	$erreur .="L'image choisie doit être en .jpg. <br>";
		 	}

	       	if ($erreur == "") //Si aucune erreur, Ok
			{
        		$filename = $_FILES['filephoto']['name'][0];
            	$sql = "UPDATE vendeur SET ID_photo = '$filename' WHERE ID = '$id';";
            	$result = mysqli_query($db_handle, $sql);
		    }
	   }
	   //Deuxième bouton : modifier le fond
	   if (isset($_POST["buttonmodifierimagefond"])) 
	   {
	   		//erreur condition pour image de fond 
		 	if ($_FILES['filephotofond']['name'][0] =="") 
		 	{ //Si aucun fichier détécté à la pos 0
		 	 	$erreur .= "Aucune image de fond n'a été ajouté. <br>";
		 	}
		 	if(count($_FILES['filephotofond']['name']) != 1)
		 	{ //Si deuxième fichier détécté
		 		$erreur .= "Vous devez choisir qu'une seule image de fond. <br>";
		 	}
		 	if (count($_FILES['filephotofond']['name']) == 1 && $_FILES['filephotofond']['type'][0] != "image/jpeg" && $_FILES['filephotofond']['name'][0] != "")
		 	{	//Format non désiré
		 	 	$erreur .="L'image de fond choisie doit être en .jpg. <br>";
		 	}
		 	if($erreur == "") //Si aucun erreur, ok 
		 	{
      			$filename = $_FILES['filephotofond']['name'][0];
            	$sql = "UPDATE vendeur SET ID_image_fond = '$filename' WHERE ID = '$id';";
            	$result = mysqli_query($db_handle, $sql);
	        }
	   }
	   ///FIN : si modification données vendeurs:

	   ///DEBUT : si modification données Acheteurs:
	   if (isset($_POST["bontonaddcoords"])) { //modification coordonnées
			$adresseUn = isset($_POST["adresseUn"])? $_POST["adresseUn"] : ""; 
			$adresseDeux = isset($_POST["adresseDeux"])? $_POST["adresseDeux"] : ""; 
			$ville = isset($_POST["ville"])? $_POST["ville"] : ""; 
			$codePostal = isset($_POST["codePostal"])? $_POST["codePostal"] : ""; 
			$pays = isset($_POST["pays"])? $_POST["pays"] : ""; 
			$telephone = isset($_POST["telephone"])? $_POST["telephone"] : ""; 
			//On fait la MAJ
			$sql = "UPDATE acheteur SET Adresse_ligne1 = '$adresseUn', Adresse_ligne2 = '$adresseDeux', Ville = '$ville', Code_postal ='$codePostal', Pays = '$pays', Telephone = '$telephone' WHERE ID = '$id';";
			$result = mysqli_query($db_handle, $sql);
	    }
	    if (isset($_POST["boutonajoutcarte"])) { //modification carte 
	    	$typecarte = isset($_POST["typecarte"])? $_POST["typecarte"] : ""; 
			$numero_carte = isset($_POST["numero_carte"])? $_POST["numero_carte"] : ""; 
			$titulaire_carte = isset($_POST["titulaire_carte"])? $_POST["titulaire_carte"] : ""; 
			$date_exp_carte = isset($_POST["date_exp_carte"])? $_POST["date_exp_carte"] : ""; 
			$mdp = isset($_POST["mdpasse"])? $_POST["mdpasse"] : ""; 
			$solde = 1500;
			//On fait la MAJ
			$sql = "UPDATE acheteur SET Type_carte = '$typecarte', Numero_carte = '$numero_carte', Nom_carte = '$titulaire_carte' , Date_exp_carte = '$date_exp_carte', Code_securite = '$mdp', Solde = '$solde' WHERE ID = '$id';";
			$result = mysqli_query($db_handle, $sql);
		}
	   ///FIN : si modification données Acheteurs:
	}
	else
	{
		echo "Database not found";
	}
	//fermer la connexion
	mysqli_close($db_handle);
	   
?>