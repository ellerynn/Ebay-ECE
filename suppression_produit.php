<?php
//CODE QUI PERMET DE SUPPRMIER UN PRODUIT,VIA UN VENDEUR OU VIA UN ADMIN
	include("const.php");
	// On prolonge la session
	session_start();

	// On teste si la variable de session existe et contient une valeur
	if(isset($_SESSION['login']))
	{
		$login = $_SESSION['login'];
		$psw = $_SESSION['psw'];
		$statut = $_SESSION['Statut'];
		$idv = $_SESSION['ID'];
	}
	//DECLARATION DES VARIABLES 

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
	$database = "ebay ece paris";				        

	if ($id == "")
	 	$erreur .= "Champ ID de l'article vide. <br>";

	if (isset($_POST["buttonsupprimer"])) 
    {
    	if ($erreur == "") 
		{
		    //identifier votre BDD
		    $database = "ebay ece paris";
		    $db_handle = mysqli_connect('localhost', 'root', '');
		    $db_found = mysqli_select_db($db_handle, $database);
			
			///BDD
	        if ($db_found) 
	        {
	           	//Vérification si l'item est déjà existant:
	           	$sql = "SELECT * FROM item WHERE ID_item LIKE '$id'";
	           	$result = mysqli_query($db_handle, $sql);
	           	//condition pour verifier les varibles si elles sont vides ou non
	                	
				if ($id != "") 
				{
					$sql .= " WHERE ID_item LIKE '%$id%'"; //id de l'item
					if ($idv != "") 
						$sql .= " AND ID_vendeur LIKE '%$idv%'";// id du vendeur	
				}
					
				$dat = "";
				//on affecte a la variable dat l'id du vendeur
                while ($data = mysqli_fetch_assoc($result)) 
                {
                    $dat =$data['ID_vendeur'];
                }

				if (mysqli_num_rows($result) == 0 ||   $dat != $idv) 
				{
					//echo "Erreur, cet item n'existe pas ou ne vous appartient pas. <br>";
				}

				else 
				{
					while ($data = mysqli_fetch_assoc($result) ) 
					{
						$id = $data['ID_item']; //affectation de la variable id avec l'id de l'item
						echo "<br>";
					}
					//on supprimer l'article 
					$sql = "DELETE FROM item";
					$sql .= " WHERE ID_item = $id";
					$result = mysqli_query($db_handle, $sql);
					//echo "Suppression réussi ! . <br>";
					//on selectionne les photos de l'item pour aussi les supprimer, meme principe
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
					//echo "Suppression des photos réussi ! . <br>";

					//Suppression des items dans la liste d'enchere
					$sql = "DELETE FROM liste_enchere WHERE ID_item = $id";
					$result = mysqli_query($db_handle, $sql);

					//Suppression des items dans encherir, si l'item n'existe plus, les données des clients qui ont enchérir sur l'item disparait
					$sql = "DELETE FROM encherir WHERE ID_item = $id";
					$result = mysqli_query($db_handle, $sql);
					//De même dans la table meilleur_offre
					$sql = "DELETE FROM meilleur_offre WHERE ID_item = $id";
					$result = mysqli_query($db_handle, $sql);
					//De même pour le panier du client
					$sql = "DELETE FROM panier WHERE ID_item = $id";
					$result = mysqli_query($db_handle, $sql);
				}
			}
		    else 
		        echo "Database not found";
	        
	        //fermer la connexion
	    	mysqli_close($db_handle); 
	    }   
	    else 
	    	echo "Erreur : <br>$erreur";
	}  
	header('Location: vendre.php')
?>