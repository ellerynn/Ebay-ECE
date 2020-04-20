<?php
	// On prolonge la session
	//sur cette page l'acheteur peut voir en détail l'item en question 
	//Les photos, le nom, La catégorie d'objet, Le vendeur,la description et la vidéo si le vendeur en a ajoutée une, Le prix si on peut acheter en achat immédiat ou faire un offre.
	//Si une enchère est possible avec cet item, le prix actuelle de l'enchère est affiché également
	//C'est sur cette page également que l'acheteur peut mettre dans son panier l'item

	session_start();
	if(isset($_SESSION['itemClick']))
	{
		$item_clique = $_SESSION['itemClick'];
		$login = $_SESSION['login'];
		$psw = $_SESSION['psw'];
		$statut = $_SESSION['Statut'];
		$id = $_SESSION['ID'];
	}
	else
	{
	  header('Location: achat.php');
	  exit();
	}

	$ID_temporaire_item = $item_clique;
	$ID_temporaire_acheteur = $id ;

	$votre_prix = isset($_POST["votre_prix"])? $_POST["votre_prix"] : "";
	$votre_prix_offre = isset($_POST["votre_prix_offre"])? $_POST["votre_prix_offre"] : "";

	//BDD
	$database = "ebay ece paris";
	$db_handle = mysqli_connect('localhost', 'root', '');
	$db_found = mysqli_select_db($db_handle, $database);
	$erreur = "";

	if ($db_found) 
	{			
		//traitement des données de la date actuelle
		date_default_timezone_set('Europe/Paris');
		$today = getdate();
		//On crée la paterne de la date actuelle avec getdate Année/Mois/Jour : xxxx-xx-xx
		$date_actuelle = "";
		if (strlen($today["mon"]) != 2) //nombre en mois, si pas en 2 chiffres
			$date_actuelle .=  $today["year"]."-0".$today["mon"]."-"; //On le crée
		else
			$date_actuelle .=  $today["year"]."-".$today["mon"]."-"; //Sinon on laisse
		if(strlen($today["mday"]) !=2) //nombre en jour
			$date_actuelle .= "0".$today["mday"];
		else
			$date_actuelle .= $today["mday"];
		//On crée la paterne de l'heure actuelle avec getdate Heure/Minute/Seconde : xx:xx:xx
		$heure_actuelle = "";
		if (strlen($today["hours"]) != 2) //nombre H, si pas en 2 chiffres
			$heure_actuelle .=  "0".$today["hours"].":"; //on fait on en sorte d'avoir 2 chiffres
		else
			$heure_actuelle .= $today["hours"].":"; //Sinon on laisse

		if(strlen($today["minutes"]) !=2) //nombre M, si pas en 2 chiffres
			$heure_actuelle .= "0".$today["minutes"].":"; //on fait on en sorte d'avoir 2 chiffres
		else
			$heure_actuelle .= $today["minutes"].":"; //Sinon on laisse
		if(strlen($today["seconds"]) !=2) //nombre S, si pas en 2 chiffres
			$heure_actuelle .= "0".$today["seconds"]; //on fait on en sorte d'avoir 2 chiffres
		else
			$heure_actuelle .= $today["seconds"]; //Sinon on laisse
		//FIN traitement des données de la date actuelle

		//On Vérifie si des enchère sont terminés ou n'a pas encore débuté (code identique dans panier.php)
		//On récupère touuuut les enchères TOUUT pour vérifier leur date
		//pas que ceux de l'acheteur
		$sql = "SELECT * FROM liste_enchere"; //On cherche tout les enchères
		$result = mysqli_query($db_handle,$sql);
		if (mysqli_num_rows($result) != 0) //Si il y a des résultats
		{
			while ($data = mysqli_fetch_assoc($result)) //On récupère chaque ligne
			{
				$itemDateFin = $data['Date_fin'];
				$itemHeureFin = $data['Heure_fin'];
				$tempItem = $data['ID_item'];
				$itemDateDebut = $data['Date_debut'];
				$itemHeureDebut = $data['Heure_debut'];
				$statut = $data['Fin'];
				$dejamodif = 0;
				if ($itemDateFin < $date_actuelle) 
				{
					//Si la date de fin de l'enchère est passé
					$sqlModif = "UPDATE liste_enchere SET Fin = 1 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
					$dejamodif = 1;
				}
				if ($itemDateFin == $date_actuelle && $itemHeureFin <= $heure_actuelle && $dejamodif == 0)
				{	//Si la date de fin de l'enchère est passé
					$sqlModif = "UPDATE liste_enchere SET Fin = 1 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
					$dejamodif = 1;
				}
				if ($itemDateDebut > $date_actuelle && $dejamodif == 0)
				{	//Si pas encore ouvert
					$sqlModif = "UPDATE liste_enchere SET Fin = 2 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
					$dejamodif = 1;
				}
				if ($itemDateDebut == $date_actuelle && $itemHeureDebut > $heure_actuelle && $dejamodif == 0)
				{	//Si pas encore ouvert
					$sqlModif = "UPDATE liste_enchere SET Fin = 2 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
					$dejamodif = 1;
				}
				if ($itemDateDebut < $date_actuelle && $dejamodif == 0) //Si la date de début est dans le passé
				{	//On ouvre l'enchère
					$sqlModif = "UPDATE liste_enchere SET Fin = 0 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
					$dejamodif = 1;
				}
				if ($itemDateDebut == $date_actuelle && $itemHeureDebut <= $heure_actuelle && $dejamodif == 0)
				{	//Si on est au même date et que l'heure est dans le passé, onouvre l'enchère
					$sqlModif = "UPDATE liste_enchere SET Fin = 0 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
					$dejamodif = 1;
				}
			}
		}
		
		//Données pour AFFICHAGE
		//Récuperation les donnees de la table item de l'item en particulier
		$sql = "SELECT * FROM item WHERE ID_item LIKE '$ID_temporaire_item'";
		$result = mysqli_query($db_handle, $sql);
		$nom_item ="";
		$vendeurItem = "";
		$ID_type_vente = "";
		$description = "";
		$categorie = "";
		$prix = "";
		$video = "";
			if (mysqli_num_rows($result) == 0) 
			{	
				$erreur.= "Erreur, cet item n'est pas disponible. <br>";
			} 
			else {
					while ($data = mysqli_fetch_assoc($result) ) 
					{	//On les récupère
						$nom_item = $data['Nom_item'];
						$ID_type_vente = $data['ID_type_vente'];
						$description = $data['Description'];
						$categorie = $data['Categorie'];
						$prix = $data['Prix'];
						$video = $data['Video'];

						$ID_vendeur = $data['ID_vendeur']; //On récupère l'ID du vendeur
						$sql1 = "SELECT Pseudo FROM vendeur WHERE ID = $ID_vendeur"; //On cherche son pseudo
						$result1 = mysqli_query($db_handle, $sql1);
						if (mysqli_num_rows($result1) != 0) 
						{	//s'il existe
							while ($data1 = mysqli_fetch_assoc($result1) )
							{	//On récupère
								$vendeurItem = "[Vendeur] ".$data1['Pseudo'];
							} 
						}
						else
						{	//S'il existe pas, c'est un administrateur
							$sql2 = "SELECT Prenom FROM personne WHERE ID = $ID_vendeur";
							$result2 = mysqli_query($db_handle, $sql2);
							if (mysqli_num_rows($result2) != 0) 
							{
								while ($data2 = mysqli_fetch_assoc($result2) )
								{
									$vendeurItem = "[Admin] ".$data2['Prenom'];
								}
							}

						}
				}
			}

		//Récuperation donnée table photo
		$sql = "SELECT * FROM photo WHERE ID_item LIKE '$ID_temporaire_item'";
		$result = mysqli_query($db_handle, $sql);
		$nom_photo = array();
		$nom_photo[0] = ""; //Au cas ou si photo innéxistant, ce qui ne devrait pas arriver
		if (mysqli_num_rows($result) == 0) {
			$erreur.= "Erreur, photo non trouvé pour cet item. <br>";
		} 
		else {
			$i = 0;
			while ($data = mysqli_fetch_assoc($result) ) 
			{
				$nom_photo[$i] = $data['Nom_photo'];
				$i++;
			}
		}
		//Récuperation des données de l'item en particulier de la table list_enchere
		$sql = "SELECT * FROM liste_enchere WHERE ID_item LIKE '$ID_temporaire_item'";
		$result = mysqli_query($db_handle, $sql);
		$ID_enchere ="";
		$Date_debut = "";
		$Heure_debut ="";
		$Date_fin ="";
		$Heure_fin ="";
		$Prixactuelle = "";
		$Prixsecond = "";
		$fin = "";
		if (mysqli_num_rows($result) != 0)
		{	//Si trouvé
			while ($data = mysqli_fetch_assoc($result)) 
			{	//On récupère ses données
				$ID_enchere = $data['ID_enchere'];
				$Date_debut = $data['Date_debut'];
				$Heure_debut = $data['Heure_debut'];
				$Date_fin= $data['Date_fin'];
				$Heure_fin = $data['Heure_fin'];
				$Prixactuelle = $data['Prix_premier'];
				$Prixsecond = $data['Prix_second'];
				$fin = $data['Fin'];
			}
		}
		//FIN DE LA PARTIE des données pour AFFICHAGE DE l'ITEM
	    
	    //Recuperation ligne si acheteur a déjà mis l'item dans son panier (car l'user n'a pas le droit de faire 2 types d'achat sur un même item)
    	$sqlVerif = "SELECT * FROM panier WHERE ID LIKE $ID_temporaire_acheteur AND ID_item LIKE $ID_temporaire_item";
    	$resultVerif = mysqli_query($db_handle, $sqlVerif);
    	$type_achat = "";
    	if (mysqli_num_rows($resultVerif) != 0) // Si il existe
	    	while ($data = mysqli_fetch_assoc($resultVerif)) 
	        {	//On récupère son type d'achat, pour éviter d'autre achat sur cet item
	            $type_achat =$data['ID_type_vente'];
	        }

	    //PARTIE ACHAT IMMEDIAT
	    //SI l'acheteur clique sur un bouton d'achat de toute façon si il a déjà mis cet objet dans son panier ça ne va pas add l'item car les clés primaires son ID de l'acheteur et l'ID de l'item.
	    $erreurAchat = "";
	    if(isset($_POST["buttonachat"])) //Si bouton achat immédiat
	    	{
		    	if (mysqli_num_rows($resultVerif) != 0 && $type_achat != "achat_immediat")
		    	{	//S'il existe dans son panier, et qu'il ne s'agit pas d'un achat immédiat
		    		$erreurAchat .= "Erreur, vous ne pouvez pas faire 2 type d'achat pour un même objet<br>";
		    	}
		    	else
		    	{	//Sinon on l'ajoute (s'il est déjà ajouté, ça écrase)
					$sql = "INSERT INTO panier (ID, ID_item, ID_type_vente) VALUES ('$ID_temporaire_acheteur', '$ID_temporaire_item', 'achat_immediat');";
			    	$result = mysqli_query($db_handle, $sql);
			    	$erreurAchat .= "Ajouter avec succès dans votre panier.<br>";
		    	}
	    }
	    //PARTIE ENCHERE
	    $erreurEnchere = "";
	    if(isset($_POST["buttonenchere"])){
	    	///PREMIERE ENCHERE
	    	if (mysqli_num_rows($resultVerif) != 0 && $type_achat != "enchere"){
	    		$erreurEnchere .= "Erreur, vous ne pouvez pas faire 2 type d'achat pour un même objet<br>";
	    	}
	    	if ($votre_prix > $Prixactuelle)
	    	{

	    		if ($fin == 0)
	    		{ //Si l'enchère est ouvert
			    	if (mysqli_num_rows($resultVerif) == 0) 
			    	{ 	// Si cet item n'existe pas dans le panier de l'acheteur
			    		//feu vert dans insert dans la table ENCHERIR car l'item n'a pas été dans le panier avec un autre type
			    		//On insert dans la table enchérir
				    	$sql = "INSERT INTO encherir (ID_enchere, ID_acheteur, ID_item, Prix_acheteur) VALUES ('$ID_enchere', '$ID_temporaire_acheteur', '$ID_temporaire_item', '$votre_prix');";
				    	$result = mysqli_query($db_handle, $sql);
				    	//Dans panier
				    	$sql = "INSERT INTO panier (ID, ID_item, ID_type_vente) VALUES ('$ID_temporaire_acheteur', '$ID_temporaire_item', 'enchere');";
				    	$result = mysqli_query($db_handle, $sql);
				    	//On met à jour les données dans la table liste_enchere (les prix)
				    	$sql = "UPDATE liste_enchere SET Prix_premier = '$votre_prix' , Prix_second = '$Prixactuelle' WHERE ID_item = $ID_temporaire_item;";
						$result = mysqli_query($db_handle, $sql);
		    			$erreurEnchere .= "Votre montant a été pris en compte.<br>";

			    	}
			    	elseif($type_achat == "enchere")
			    	{ 	//si l'article existe, vérification si l'acheteur avait enchéri
			    		//Update des prix dans liste_enchere
				    	$sql3 = "UPDATE liste_enchere SET Prix_premier = '$votre_prix' , Prix_second = '$Prixactuelle' WHERE ID_item = $ID_temporaire_item;";
						$result3 = mysqli_query($db_handle, $sql3);
						//update dans la table encherir
						$sql6 = "UPDATE encherir SET Prix_acheteur = '$votre_prix' WHERE ID_acheteur = '$ID_temporaire_acheteur' AND ID_enchere = '$ID_enchere';";
						$result6 = mysqli_query($db_handle, $sql6);
		    			$erreurEnchere .= "Votre montant a été pris en compte.<br>";
			    	}
			    	//réaffecter la nouvelle valeur de prix premier
			    	$sql5 = "SELECT Prix_premier FROM liste_enchere WHERE ID_item LIKE '$ID_temporaire_item'";
					$result5 = mysqli_query($db_handle, $sql5);
					while ($data = mysqli_fetch_assoc($result5)) 
		            {
		                $Prixactuelle =$data['Prix_premier'];
		            }
		        }
		        if ($fin == 1)
		        	$erreurEnchere .= "Nous sommes navrés de vous annoncer que l'enchere est déjà terminée.<br>";
		        if ($fin == 2)
		        	$erreurEnchere .= "L'enchère n'a pas encore commencé.<br>";
	        }else
	        {	
	        	if ($fin == 0)
	        		$erreurEnchere .= "Erreur, vous ne pouvez pas mettre un prix inferieur au prix actuel.<br>";
	        	if ($fin == 1)
	        		$erreurEnchere .= "Nous sommes navrés de vous annoncer que l'enchere est déjà terminée.<br>";
	       		if ($fin == 2)
	       			$erreurEnchere .= "L'enchère n'a pas encore commencé.<br>";
	        }
		    	
	    }

	    //PARTIE OFFRE
	    $tenta = "";
		$stat = "";
		$prix_client = "";
    	//Recupération du prix du vendeur si une offre a été faite par l'acheteur sur cette item: (Normalement tenta >= 1
	    $sqlOffre = "SELECT * from meilleur_offre WHERE ID_item = $ID_temporaire_item AND ID_acheteur LIKE '$ID_temporaire_acheteur' AND ID_vendeur LIKE '$ID_vendeur'";
	    $resultOffre = mysqli_query($db_handle, $sqlOffre);
	    if (mysqli_num_rows($resultOffre) != 0) 
	    {	//S'il existe
			while ($data = mysqli_fetch_assoc($resultOffre)) 
			{	//On récupère ses données
				$prix_vendeur = $data['Prix_vendeur'];
				$prix_client = $data['Prix_acheteur'];
				$tenta = $data['Tentative'];
				$stat = $data['Statut'];
			}
		}
		//Si première offre du client sur cet item, on regarde si l'item n'a pas déjà été accepté par quelqu'un avant de faire une première offre
		$sqlVerifSatutItem = "SELECT * FROM meilleur_offre WHERE ID_item = $ID_temporaire_item AND Statut = 3";
		$resultVerifStatutItem = mysqli_query($db_handle, $sqlVerifSatutItem);

	    $erreurOffre = "";
	    if(isset($_POST["buttonoffre"]))
	    {
	    	if (mysqli_num_rows($resultVerif) != 0 && $type_achat != "offre")
	    	{	//Si l'objet existe dans son panier et n'est pas un offre
	    		$erreurOffre .= "Erreur, vous ne pouvez pas faire 2 type d'achat pour un même objet<br>";
	    	}
	    	if (mysqli_num_rows($resultVerifStatutItem) != 0)
	    	{	//Si premier offre, et que l'offre a déjà été accpeté pour quelqu'un d'autre
	    		$erreurOffre .= "Cet item a été accpeté pour quelqu'un.<br>";
	    	}

	    	if ($votre_prix_offre < $prix && $votre_prix_offre != "")
	    	{	//Si le prix entré est correct
		    	if (mysqli_num_rows($resultVerif) == 0 && mysqli_num_rows($resultOffre) == 0 && mysqli_num_rows($resultVerifStatutItem) == 0 )
	            {	//Si pas dans le panier, et pas fait d'offre auparavant,et que l'offre n'a pas encore été 			accepté pour quelqu'un
	            	if(isset($_POST["clause"]))
	            	{	//Si la clause est cochée
	            		//On ajoute les données dans la table meilleur_offre
				    	$sql = "INSERT INTO meilleur_offre (ID_acheteur, ID_vendeur, ID_item, Prix_acheteur, Prix_vendeur, Tentative, Statut) VALUES ('$ID_temporaire_acheteur', '$ID_vendeur', '$ID_temporaire_item', '$votre_prix_offre', '$prix', '1', '2');";
				    	$result = mysqli_query($db_handle, $sql);
				    	//Dans la table PANIER
				    	$sql2 = "INSERT INTO panier (ID, ID_item, ID_type_vente) VALUES ('$ID_temporaire_acheteur', '$ID_temporaire_item', 'offre');";
				    	$result2 = mysqli_query($db_handle, $sql2);
				    	$erreurOffre .= "Merci de votre demande, nous la transmettrons au vendeur. S'il l'accepte, vous pourrez acheter le produit, sinon faites une meilleure offre ou supprimer. Cependant si vous ne voulez plus faire d'offre soyez sûr, car vous ne pourrez plus retenter.<br>";
	            	}

	            }elseif($type_achat == "offre")
	            { 	//L'utilisateur a déjà fait un offre sur cet item
			    	if ($tenta < 5 && $stat == 1)
			    	{ 	//Son tour, et qu'il a pas encore atteint ses 5 tentatives
			    		//On met à jour la table meilleur_offre
			    		$tenta++;
			    		$sql3 = "UPDATE meilleur_offre SET Prix_acheteur = '$votre_prix_offre' , Statut = '2', Tentative = '$tenta' WHERE ID_acheteur = '$ID_temporaire_acheteur' AND ID_vendeur = '$ID_vendeur' AND ID_item = '$ID_temporaire_item';";
						$result3 = mysqli_query($db_handle, $sql3);
			    		$erreurOffre .= "Merci de votre demande, nous la transmettrons au vendeur. S'il l'accepte, vous pourrez acheter le produit, sinon faites une meilleure offre ou supprimer. Cependant si vous ne voulez plus faire d'offre soyez sûr, car vous ne pourrez plus retenter<br>";

			    	}
			    	if ($tenta == 5 && $stat == 1) //Tour du client et les tentatives atteints
			    		$erreurOffre .= "Vous ne pouvez plus faire de tentative, le vendeur vous a répondu, Accecptez ou refusez dans le panier.<br>";
			    	if($tenta == 5 && $stat == 2) //statut 2 = tour du vendeur et tentatives atteints
						$erreurOffre .= "Vous avez atteint le nombre limite de demande, vous ne pouvez plus faire de demande ! Attendez la réponse du vendeur.<br>";
			    	if ($stat == 2 && $tenta != 5) //si tenta != 5 et tour du vendeur
			    		$erreurOffre .= "Patientez, la demande d'offre précédente n'a pas encore eu de réponse de la part du vendeur. Votre précédente offre est de ".$prix_client." euros <br>";
			    	if ($stat == 3) //statut 3 : faut payer
						$erreurOffre .= "Le vendeur a accepté pour votre offre au prix de ".$prix_client." euros. Veuillez vous dirigez au panier pour régler.<br>";
					if ($stat == 4) //statut 4 : le produit a déjà été accepté pour quelqu'un d'autre
						$erreurOffre .="Nous sommes navrés de vous annoncer que le produit a été vendu à un autre client.<br>";
			    }
			    if (mysqli_num_rows($resultVerif) == 0 && mysqli_num_rows($resultOffre) != 0)
			    {	//Si dans meilleur_offre mais pas dans panier 
			    	if ($stat == 5) //L'acheteur a supprimé
						$erreurOffre .="Vous avez déjà tenté votre chance.<br>";
			    }

			}else
			{
				$erreurOffre .= "Erreur, vous ne pouvez pas mettre un prix vide ou supérieur/égal au prix actuel.<br>";
			}
	    }
	}
	 else 
	 {
	      echo "Database not found";
	 } 
	
?>
<!DOCTYPE html> 
<html> 
	<head>
		<title>Ebay ECE</title>  
		<meta charset="utf-8">  
		
		<!--Charger Bootstrap via CDN-->
		<meta name="viewport" content="width=device-width, initial-scale=1">     
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

		<!--Inclure jQuery-->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>  
		
		<!--Charger le code JavaScript de Bootstrap-->
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>  

		<!--7. Inclure des CSS personnalisés-->
		<link rel="stylesheet" type="text/css" href="style.css"> 
		
		<!--Image de fond et JavaScript personnalisé-->
		<script type="text/javascript">      
			$(document).ready(function() {           
				$('.header').height($(window).height()); 
			}); 
		</script> 

		<!--Icones personnalisés-->
		<script src="https://kit.fontawesome.com/58c71aba33.js" crossorigin="anonymous"></script>
	</head> 	

	<body> 
		<!--Ajouter une barre de navigation-->
		<nav class="navbar navbar-expand-md fixed-top"> <!--indique à quel point la barre de navigation passe d'une icône verticale à une barre horizontale pleine grandeur. Ici défini sur les écrans moyens = supérieur à 768 pixels.-->
			<button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#main-navigation"> <!--navbar-toggler — Indique le bouton bascule du menu réduit.-->   
				<span class="navbar-toggler-icon"></span> <!--navbar-toggler-icon — crée l'icône-->      
			</button>   

			<form class="navbar-form inline-form">
				<div class="form-group">
				  	<span style="color:white;"><i class="fas fa-search"></i></span>
				   	<input type="search" class="input-sm form-control-sm" placeholder="Rechercher sur eBay ECE">
				   	<button class="btn btn-outline-secondary btn-sm">Chercher</button>
				</div>
			</form>

			<div class="collapse navbar-collapse">     
				<ul class="navbar-nav"> <!--navbar-nav — La classe de l'élément de liste <ul> qui contient les éléments de menu. Ces derniers sont notés avec nav-item et nav-link.-->
					<li class="nav-item">
						<a class="nav-link" href="accueil.php">Accueil</a>
					</li>
					<li class="nav-item dropdown">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Mon eBay</button>
					  	<div class="dropdown-menu" id="menu-deroulant">
						    <a class="nav-link dropdown-item" href="achat.php">Achat</a>
					  	</div>
					</li>
					<li class="nav-item dropdown">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"><i class="fas fa-user"></i></button>
					  	<div class="dropdown-menu" id="menu-deroulant">
						    <a class="nav-link dropdown-item" href="votre_compte.php">Mon compte</a>
						    <a class="nav-link dropdown-item" href="deconnexion.php">Se déconnecter</a>
					  	</div>
					</li> 
					<li class="nav-item">
						<a class="nav-link" href="panier.php" id="panier"><i class="fas fa-shopping-cart"></i></a>
					</li>    
				</ul>      
			</div> 
		</nav>
		<br><br><br><br>
		
		<div class="container-fluid features">
			<div class="row">
				<div class="col-lg-2 col-md-2 col-sm-12"></div>
	        	<div class="col-lg-4 col-md-4 col-sm-12">
	        		<div style="margin:2em; width: 350px; height: 350px;" id="carousel" class="carousel slide" data-ride="carousel">
					 	<ul class="carousel-indicators">
					 	<?php
			            	for ($i = 0 ; $i < count($nom_photo); $i++)
			            	{
							    echo '<li data-target="#carousel" data-slide-to="'.$i.'"></li>';
							}?>
						</ul>
						<div class = "carousel-inner">
							<?php
								for ($i = 0 ; $i < count($nom_photo); $i++)
				            	{
									    if ($i == 0)
									    echo'
										    <div class="carousel-item active">
										      	<img src = "images_web/'.$nom_photo[$i].'" height=350 width =350 >
										    </div>';
									    else
									    echo'
										    <div class="carousel-item">
										      	<img src = "images_web/'.$nom_photo[$i].'" height=350 width =350 >
										    </div>'; 
								}
								if($video != "" || $video != 0 )
								{
									echo'
									<div class="carousel-item">
				                    	<div class="embed-responsive embed-responsive-4by3">;
				                    		<iframe class="embed-responsive-item" src="'.$video.'" height=350 width =350 	></iframe>
				                    	</div>
				                    </div>'; 
				                }		
								?>
						</div>
						<a class="carousel-control-prev" href="#carousel" data-slide="prev">
						    <i style="color: black;"class="fas fa-chevron-left"></i>
						</a>
						<a class="carousel-control-next" href="#carousel" data-slide="next">
						    <i style="color: black;"class="fas fa-chevron-right"></i>
						</a>
					</div>
				</div> <br>
				<div class="col-lg-4 col-md-4 col-sm-12">
				<?php 
				//Affichage données de l'item
					echo'<h1>'.$nom_item.'<br></h1>';
					
					if($categorie == "Ferraille_tresor")
						echo "Catégorie : Ferraille ou trésor. <br>";

					if($categorie == "VIP")
						echo "Catégorie : Accessoires VIP. <br>";

					if($categorie == "Musee")
						echo "Catégorie : Bon pour le Musée. <br>";
					echo "Vendu par : ".$vendeurItem."<br>";
					echo "Description de l'article : <br>".$description."<br>";
					
					if (strpos($ID_type_vente, "achat_immediat") !== FALSE || strpos($ID_type_vente, "offre") !== FALSE)
						echo "Prix : ".$prix."€<br>";
						//Il s'affiche que quand c'est achat immédiat ou offre

					echo '<form action="" method="post">';
					if (strpos($ID_type_vente, "achat_immediat") !== FALSE)
						echo "Achat immédiat possible. <br>";
					//Si l'objet peut être vendu en achat immediat
					if (strpos($ID_type_vente, "achat_immediat") !== FALSE)
					{	//Bouton pour ajouter dans le panier comme achat immédiat
						echo '<input class="btn border btn-outline-secondary rounded-lg" name="buttonachat" type="submit" value="Ajouter dans le panier" onclick="montrerErreur()"><br><br>';
						if ($erreurAchat != "")
						{
							echo $erreurAchat;
							$erreurAchat = "";
						}
					}

					if (strpos($ID_type_vente, "offre") !== FALSE)
						echo "Meilleure offre possible. <br>";
					//Si l'objet peut être vendu en offre
					//meilleur offre formulaire
					if (strpos($ID_type_vente, "offre") !== FALSE)
					{	//Le prix à rentrer
						echo '<td><input type="number" name="votre_prix_offre" placeholder="Votre offre"></td>';
						if (mysqli_num_rows($resultOffre) != 0)
							echo '<p>Le prix actuel est de '.$prix_vendeur.', veuillez mettre un prix inférieur au prix actuel si vous souhaitez négocier</p>'; //Indicateur
						else 
							echo '<p>Le prix actuel est de '.$prix.', veuillez mettre un prix inférieur au prix actuel si vous souhaitez négocier</p>';	//Indicateur
						if ($erreurOffre != ""){
							echo $erreurOffre;
							$erreurOffre = "";
						}
						//Clause
						echo '<div class="form-group">';
			          	echo '<p class="font-weight-bold">Clause</p>';
			           	echo '<input type="checkbox" name="clause" value="clause"  requiered>Accepte d\'être sous le contrat légal pour acheter l\'article si le vendeur accepte l\'offre <br>';
			           	if(isset($_POST["clause"]))
			           	{
			           		echo"Merci d'avoir accepté la clause ! <br>";
			           	}
			           	else
			           		echo "Vous êtes obligés d'accepter la clause ! <br>";

						echo '</div>';
						echo '<input class="btn border btn-outline-secondary rounded-lg" name="buttonoffre" type="submit" value="Faire la demande">';
					}

					//Si l'objet peut être vendu en enchère
					//enchere formulaire
					if (strpos($ID_type_vente, "enchere") !== FALSE)
					{
						if ($fin == 0)
						echo "L'enchère est ouverte ! <br>";
						elseif ($fin == 1)
							echo "L'enchère est fermée ! <br>";
						elseif ($fin == 2)
							echo "L'enchère n'est pas encore ouverte : <br>";
						echo "Début : ".$Date_debut." à ".$Heure_debut."<br>";
						echo "Fin : ".$Date_fin." à ".$Heure_fin."<br>";
						echo '<td><input type="number" name="votre_prix" placeholder="Votre prix"></td>';
						echo '<p>Le prix actuel est de '.$Prixactuelle.'€, veuillez saisir un montant supérieur.</p>';
						
						if ($erreurEnchere != "")
						{
							echo $erreurEnchere;
							$erreurEnchere = "";
						}	
						echo '<input class="btn border btn-outline-secondary rounded-lg" name="buttonenchere" type="submit" value="Enchérir">';
					}
					echo "</form><br><br><br><br>";
		            ?>
		    	</div>
		    	<div class="col-lg-2 col-md-2 col-sm-12"></div>
		    </div>
	    </div>
	    <br>
		<footer class="page-footer container-fluid">   
			<div class="container">    
				<div class="row">       
					<div class="col-lg-3 col-md-3 col-sm-12">	
						<h5 class="text-uppercase font-weight-bold">Catégories</h5>
						<ul>  
							<li>
								Ferraille ou Trésor
							</li>    
							<li>
								Bon pour le Musée
							</li> 
							<li>
								Accessoires VIP
							</li>               
						</ul> 
					</div> 
					<div class="col-lg-3 col-md-3 col-sm-12">	
						<a href="achat.php" id="achat"><h5 class="text-uppercase font-weight-bold">Achat</h5></a>
						<ul>  
							<li>
								<a href="#" id="enchere">Enchères</a>
							</li>    
							<li>
								<a href="#" id="achetez">Achetez-le maintenant</a>
							</li> 
							<li>
								<a href="#" id="offre">Meilleure offre</a>
							</li>               
						</ul> 
					</div>   
					<div class="col-lg-3 col-md-3 col-sm-12">	
						<ul>  
							<li>
								<h5 class="text-uppercase font-weight-bold">Vendre</h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"> <a href="votre_compte.php">Votre compte</a> </h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold">Admin </h5>
							</li>            
						</ul> 
					</div> 

					<div class="col-lg-3 col-md-3 col-sm-12">       
						<h5 class="text-uppercase font-weight-bold">Contact</h5>       
						<p> 37, quai de Grenelle, 75015 Paris, France <br>             
							info@webDynamique.ece.fr <br>             
							+33 01 02 03 04 05 <br>             
							+33 01 03 02 05 04 </p>     
					</div>   
				</div>   
				<div class="footer-copyright text-center"> 
					<small>&copy; 2019 Copyright | Droit d'auteur: webDynamique.ece.fr</small>					
				</div> 
			</div>
		</footer>
	</body> 
</html> 