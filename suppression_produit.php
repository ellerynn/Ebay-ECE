<?php
//vérifier si c'est bien un vendeur 
//extraire ID du fichier .txt pour enregistrer l'id dans la table item
//demander le nom, 
//les photos(autre table) [la partie photo à faire quand on aura fait tout les autres] //enregistre le nom de l'image
//Une vidéo(si optionnel: choix), la description, choix de catégorie, prix

$ID_VENDEUR_TEMPORAIRE = "4";


//suppression item

	 $id = isset($_POST["id"])? $_POST["id"] : "";
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

	 if ($id == "") { 
	 	$erreur .= "Champ ID de l'article vide. <br>"; }
 	 
	 if ($erreur == "") 
	 {

	     //identifier votre BDD
	     $database = "ebay ece paris";

	     $db_handle = mysqli_connect('localhost', 'root', '');
	     $db_found = mysqli_select_db($db_handle, $database);
	           if (isset($_POST["buttonsupprimer"])) 
	           {
					 ///BDD
	                if ($db_found) 
	                {
	                	//Vérification si le nom est déjà existant:
	                	$sql = "SELECT * FROM item WHERE ID_item LIKE '$id'";
	                	$result = mysqli_query($db_handle, $sql);
	                	
							if ($id != "") 
							{
								$sql .= " WHERE ID_item LIKE '%$id%'";
								if ($ID_VENDEUR_TEMPORAIRE != "") {
									$sql .= " AND ID_vendeur LIKE '%$ID_VENDEUR_TEMPORAIRE%'";
								}
							}
							$dat = "";
                            while ($data = mysqli_fetch_assoc($result)) 
                            {
                                $dat =$data['ID_vendeur'];
                            }
							if (mysqli_num_rows($result) == 0 ||   $dat != $ID_VENDEUR_TEMPORAIRE) {
								//Livre inexistant
								echo "Erreur, cet item n'existe pas ou ne vous appartient pas. <br>";
							} 
							else {
								while ($data = mysqli_fetch_assoc($result) ) 
								{
									$id = $data['ID_item'];
									echo "<br>";
								}
								$sql = "DELETE FROM item";
								$sql .= " WHERE ID_item = $id";
								$result = mysqli_query($db_handle, $sql);
								echo "Suppression réussi ! . <br>";

								$sqlphoto = "SELECT * FROM photo WHERE ID_item LIKE '$id'";
	                			$resultphoto = mysqli_query($db_handle, $sqlphoto);
	                			while ($data = mysqli_fetch_assoc($resultphoto) ) 
								{
									$id = $data['ID_item'];
									echo "<br>";
								}
								$sql2 = "DELETE FROM photo";
								$sql2 .= " WHERE ID_item = $id";
								$result = mysqli_query($db_handle, $sql2);
								echo "Suppression des photos réussi ! . <br>";

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