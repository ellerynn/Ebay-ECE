<?php
	// On prolonge la session
	//LES MESSAGES D'ERREURS SONT POTENTIELLEMENT A REVOIR
	
	//sur cette page l'acheteur peut voir en détail l'item en question 
	//Les photos, le nom, La catégorie d'objet, Le vendeur, Le prix si on peut acheter en achat immédiat ou faire un offre, 
	//La description accompagné d'une vidéo si le vendeur en a ajouté
	//Si une enchère est possible avec cet item, le prix actuelle de l'enchère est affiché également
	//C'est sur cette page également que l'acheteur peut mettre dans son panier avec des boutons 

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
	$database = "ebay ece paris";
	$db_handle = mysqli_connect('localhost', 'root', '');
	$db_found = mysqli_select_db($db_handle, $database);

	if ($db_found) 
	{			
		//traitement des données de la date actuelle
		date_default_timezone_set('Europe/Paris');
		$today = getdate();
		$date_actuelle = "";
		if (strlen($today["mon"]) != 2) //nombre en mois
			$date_actuelle .=  $today["year"]."-0".$today["mon"]."-";
		else
			$date_actuelle .=  $today["year"]."-".$today["mon"]."-";
		if(strlen($today["mday"]) !=2) //nombre en jour
			$date_actuelle .= "0".$today["mday"];
		else
			$date_actuelle .= $today["mday"];

		$heure_actuelle = "";
		if (strlen($today["hours"]) != 2) //nombre H
			$heure_actuelle .=  "0".$today["hours"].":";
		else
			$heure_actuelle .= $today["hours"].":";

		if(strlen($today["minutes"]) !=2) //nombre M
			$heure_actuelle .= "0".$today["minutes"].":";
		else
			$heure_actuelle .= $today["minutes"].":";
		if(strlen($today["seconds"]) !=2) //nombre S
			$heure_actuelle .= "0".$today["seconds"];
		else
			$heure_actuelle .= $today["seconds"];
		//FIN traitement des données de la date actuelle

		//On Vérifie si des enchère sont terminés (code identique dans panier.php)
		//On récupère touuuut les enchères TOUUT pour vérifier leur date
		//pas que acheteur
		$sql = "SELECT * FROM liste_enchere";
		$result = mysqli_query($db_handle,$sql);
		if (mysqli_num_rows($result) != 0)
		{
			while ($data = mysqli_fetch_assoc($result)) 
			{
				$itemDateFin = $data['Date_fin'];
				$itemHeureFin = $data['Heure_fin'];
				$tempItem = $data['ID_item'];
				if ($itemDateFin < $date_actuelle)
				{
					$sqlModif = "UPDATE liste_enchere SET Fin = 1 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
				}
				if ($itemDateFin == $date_actuelle && $itemHeureFin <= $heure_actuelle)
				{
					$sqlModif = "UPDATE liste_enchere SET Fin = 1 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
				}
			}
		}
		
		//PARTIE AFFICHAGE
		//Récuperation donnee table item
		$sql = "SELECT * FROM item WHERE ID_item LIKE '$ID_temporaire_item'";
		$result = mysqli_query($db_handle, $sql);

		$nom_item ="";
		$ID_vendeur = "";
		$ID_type_vente = "";
		$description = "";
		$categorie = "";
		$prix = "";
		$video = "";
		
			if (mysqli_num_rows($result) == 0) {
				//Livre inexistant
				echo "Erreur, cet item n'est pas disponible. <br>";
			} 
			else {
				
				while ($data = mysqli_fetch_assoc($result) ) 
				{
					
					$nom_item = $data['Nom_item'];
					$ID_vendeur = $data['ID_vendeur'];
					$ID_type_vente = $data['ID_type_vente'];
					$description = $data['Description'];
					$categorie = $data['Categorie'];
					$prix = $data['Prix'];
					$video = $data['Video'];
				}
			}

		//Récuperation donnée table photo
		$sql = "SELECT * FROM photo WHERE ID_item LIKE '$ID_temporaire_item'";
		$result = mysqli_query($db_handle, $sql);
		$nom_photo = array();
		$nom_photo[0] = ""; //Au cas ou si photo innéxistant, ce qui ne devrait jamais arriver
		if (mysqli_num_rows($result) == 0) {
			echo "Erreur, cet item n'est pas disponible. <br>";
		} 
		else {
			$i = 0;
			while ($data = mysqli_fetch_assoc($result) ) 
			{
				$nom_photo[$i] = $data['Nom_photo'];
				$i++;
			}
			
		}
		//Récuperation de la table list_enchere si il y a 
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


		if (mysqli_num_rows($result) != 0){
			while ($data = mysqli_fetch_assoc($result) ) 
			{
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
		//FIN DE LA PARTIE AFFICHAGE DE l'ITEM
	    
	    //Recuperation ligne si acheteur a déjà mis l'item dans son panier (car l'user n'a pas le droit de faire 2 types d'achat sur un même item)
    	$sqlVerif = "SELECT * FROM panier WHERE ID LIKE $ID_temporaire_acheteur AND ID_item LIKE $ID_temporaire_item";
    	$resultVerif = mysqli_query($db_handle, $sqlVerif);
    	//recupération du type d'achat que l'acheteur avait voulu pour cet article si l'article exsite dans son panier
    	$type_achat = "";
    	if (mysqli_num_rows($resultVerif) != 0)
	    	while ($data = mysqli_fetch_assoc($resultVerif)) 
	        {
	            $type_achat =$data['ID_type_vente'];
	        }

	    //SI l'acheteur clique sur un bouton d'achat de toute façon si il a déjà mis cet objet dans son panier ça ne va pas add l'item car les clés primaires son ID de l'acheteur et l'ID de l'item.
	    $erreurAchat = "";
	    if(isset($_POST["buttonachat"])){
	    	if (mysqli_num_rows($resultVerif) != 0 && $type_achat != "achat_immediat"){
	    		$erreurAchat .= "Erreur, vous ne pouvez pas faire 2 type d'achat pour un même objet<br>";
	    	}else{
				$sql = "INSERT INTO panier (ID, ID_item, ID_type_vente) VALUES ('$ID_temporaire_acheteur', '$ID_temporaire_item', 'achat_immediat');";
		    	$result = mysqli_query($db_handle, $sql);
		    	$erreurAchat .= "Ajouter ave succès dans votre panier.<br>";
	    	}
	    }
	    //PARTIE ENCHERE
	    $erreurEnchere = "";
	    if(isset($_POST["buttonenchere"])){
	    	///PREMIERE ENCHERE
	    	if (mysqli_num_rows($resultVerif) != 0 && $type_achat != "enchere"){
	    		$erreurEnchere .= "Erreur, vous ne pouvez pas faire 2 type d'achat pour un même objet<br>";
	    	}
	    	if ($votre_prix > $Prixactuelle){
	    		if ($fin != 1){ //Si l'enchère n'est pas fini
			    	if (mysqli_num_rows($resultVerif) == 0) { // Si cet item n'existe pas dans le panier de l'acheteur
			    	//feu vert dans insert dans la table ENCHERIR car l'item n'a pas été dans le panier avec un autre type
				    	$sql = "INSERT INTO encherir (ID_enchere, ID_acheteur, ID_item, Prix_acheteur) VALUES ('$ID_enchere', '$ID_temporaire_acheteur', '$ID_temporaire_item', '$votre_prix');";
				    	$result = mysqli_query($db_handle, $sql);
				    	//insert dans la table PANIER vu qu'il n'existe pas dans le panier d'après mysqli_num_rows($resultVerif) == 0
				    	$sql = "INSERT INTO panier (ID, ID_item, ID_type_vente) VALUES ('$ID_temporaire_acheteur', '$ID_temporaire_item', 'enchere');";
				    	$result = mysqli_query($db_handle, $sql);
				    	//MAJ de la liste_enchere
				    	$sql = "UPDATE liste_enchere SET Prix_premier = '$votre_prix' , Prix_second = '$Prixactuelle' WHERE ID_item = $ID_temporaire_item;";
						$result = mysqli_query($db_handle, $sql);
		    			$erreurEnchere .= "Votre montant a été pris en compte.<br>";

			    	}
			    	elseif($type_achat == "enchere"){ //si l'article existe, vérification si l'acheteur avait enchéri
			    		//Update des prix dans liste_enchere
				    	$sql3 = "UPDATE liste_enchere SET Prix_premier = '$votre_prix' , Prix_second = '$Prixactuelle' WHERE ID_item = $ID_temporaire_item;";
						$result3 = mysqli_query($db_handle, $sql3);
						
						//update dans la table encherir
						$sql6 = "UPDATE encherir SET Prix_acheteur = '$votre_prix' WHERE ID_acheteur = '$ID_temporaire_acheteur' AND ID_enchere = '$ID_enchere';";
						$result6 = mysqli_query($db_handle, $sql6);
		    			$erreurEnchere .= "Votre montant a été pris en compte.<br>";
			    	}
			    	//reaffecter la nouvelle valeur de prix premier
			    	$sql5 = "SELECT Prix_premier FROM liste_enchere WHERE ID_item LIKE '$ID_temporaire_item'";
					$result5 = mysqli_query($db_handle, $sql5);
					while ($data = mysqli_fetch_assoc($result5)) 
		            {
		                $Prixactuelle =$data['Prix_premier'];
		            }
		        }else
		        	$erreurEnchere .= "Nous sommes navrés de vous annoncer que l'enchere est déjà terminée.<br>";
	        }else
	        {	
	        	if ($fin != 1)
	        		$erreurEnchere .= "Erreur, vous ne pouvez pas mettre un prix inferieur au prix actuel.<br>";
	        	else
	        		$erreurEnchere .= "Nous sommes navrés de vous annoncer que l'enchere est déjà terminée.<br>";
	        }
		    	
	    }
	    //PARTIE OFFRE

	    $tenta = "";
		$stat = "";
		$prix_client = "";
		//Partie Un, s'il avait déjà effectué un Offre !
    	//Recupération du prix du vendeur si une offre a été faite par l'acheteur sur cette item: (ICI normalement tenta >= 1
	    $sqlOffre = "SELECT * from meilleur_offre WHERE ID_item = $ID_temporaire_item AND ID_acheteur LIKE '$ID_temporaire_acheteur' AND ID_vendeur LIKE '$ID_vendeur'";
	    $resultOffre = mysqli_query($db_handle, $sqlOffre);
	    if (mysqli_num_rows($resultOffre) != 0) //il existe 
	    {
			while ($data = mysqli_fetch_assoc($resultOffre)) 
			{
				$prix_vendeur = $data['Prix_vendeur'];
				$prix_client = $data['Prix_acheteur'];
				$tenta = $data['Tentative'];
				$stat = $data['Statut'];
			}
			
		}
		//On regarde si l'item n'a pas déjà été accepté par quelqu'un avant de faire une première offre
		$sqlVerifSatutItem = "SELECT * FROM meilleur_offre WHERE ID_item = $ID_temporaire_item AND Statut = 3";
		$resultVerifStatutItem = mysqli_query($db_handle, $sqlVerifSatutItem);

	    $erreurOffre = "";
	    if(isset($_POST["buttonoffre"]))
	    {
	    	if (mysqli_num_rows($resultVerif) != 0 && $type_achat != "offre")
	    	{
	    		$erreurOffre .= "Erreur, vous ne pouvez pas faire 2 type d'achat pour un même objet<br>";
	    	}
	    	if (mysqli_num_rows($resultVerifStatutItem) != 0)
	    		$erreurOffre .= "Cet item a été accpeté pour quelqu'un.<br>";

	    	if ($votre_prix_offre < $prix && $votre_prix_offre != "")
	    	{
	    	//Indique que l'item n'a jamais été dans son panier donc l'user peut faire une offre et le mettre dans son panier
		    	if (mysqli_num_rows($resultVerif) == 0 && mysqli_num_rows($resultOffre) == 0 && mysqli_num_rows($resultVerifStatutItem) == 0 ) //Si pas fait de meilleur offre et que offre n'a pas été accepté pour qlq
	            {
	            	if(isset($_POST["clause"]))
	            	{
	            				            //insert dans la table ENCHERIR
				    	$sql = "INSERT INTO meilleur_offre (ID_acheteur, ID_vendeur, ID_item, Prix_acheteur, Prix_vendeur, Tentative, Statut) VALUES ('$ID_temporaire_acheteur', '$ID_vendeur', '$ID_temporaire_item', '$votre_prix_offre', '$prix', '1', '2');";
				    	$result = mysqli_query($db_handle, $sql);
				    	//insert dans la table PANIER
				    	$sql2 = "INSERT INTO panier (ID, ID_item, ID_type_vente) VALUES ('$ID_temporaire_acheteur', '$ID_temporaire_item', 'offre');";
				    	$result2 = mysqli_query($db_handle, $sql2);
				    	//variable test pour blindage saisit enchere inferieur
				    	$erreurOffre .= "Merci de votre demande, nous la transmettrons au vendeur. S'il l'accepte, vous pourrez acheter le produit, sinon faites une meilleure offre ou supprimer. Cependant si vous ne voulez plus faire d'offre soyez sûr, car vous ne pourrez plus retenter.<br>";
	            	}

	            }elseif($type_achat == "offre"){ //on vérifie si l'objet dans le panier est un achat en offre
			    	//Partie Deuxième ou nième <= 5 offre et que c'est son tour: 
			    	if ($tenta < 5 && $stat == 1){
			    		//L'user a entré un nouveau prix :
			    		$tenta++;
			    		$sql3 = "UPDATE meilleur_offre SET Prix_acheteur = '$votre_prix_offre' , Statut = '2', Tentative = '$tenta' WHERE ID_acheteur = '$ID_temporaire_acheteur' AND ID_vendeur = '$ID_vendeur' AND ID_item = '$ID_temporaire_item';";
						$result3 = mysqli_query($db_handle, $sql3);
			    		$erreurOffre .= "Merci de votre demande, nous la transmettrons au vendeur. S'il l'accepte, vous pourrez acheter le produit, sinon faites une meilleure offre ou supprimer. Cependant si vous ne voulez plus faire d'offre soyez sûr, car vous ne pourrez plus retenter<br>";

			    	}
			    	if ($tenta == 5 && $stat == 1) //statut 1 = tour du client
			    		$erreurOffre .= "Vous ne pouvez plus faire de tentative, le vendeur vous a répondu, Accecptez ou refusez dans le panier.<br>";
			    	if($tenta == 5 && $stat == 2) //statut 2 = tour du vendeur
						$erreurOffre .= "Vous avez atteint le nombre limite de demande, vous ne pouvez plus faire de demande ! Attendez la réponse du vendeur.<br>";
			    	if ($stat == 2 && $tenta != 5) //si tenta = 5 c'est le msg au dessus
			    		$erreurOffre .= "Patientez, la demande d'offre précédente n'a pas encore eu de réponse de la part du vendeur. Votre précédente offre est de ".$prix_client." euros <br>";
			    	if ($stat == 3) //statut 3 = c'bon plus bsn 
						$erreurOffre .= "Le vendeur a accepté pour votre offre au prix de ".$prix_client." euros. Veuillez vous dirigez au panier pour régler.<br>";
					if ($stat == 4) //statut 4 = le produit a déjà été accepté pour quelqu'un d'autre
						$erreurOffre .="Nous sommes navrés de vous annoncer que le produit a été vendu à un autre client.<br>";
			    }
			    if (mysqli_num_rows($resultVerif) == 0 && mysqli_num_rows($resultOffre) != 0)
			    {
			    	if ($stat == 5) //L'acheteur a supprimé
						$erreurOffre .="Vous avez déjà tenté votre chance.<br>";
			    }

			}else{
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
		
		<div class="row">
        	<div class="col-lg-4 col-md-4 col-sm-12">
        		<div style="margin:2em; width: 350px; height: 350px;" id="carousel" class="carousel slide" data-ride="carousel">
				 	<ul class="carousel-indicators">
				 	<?php
		            	for ($i = 0 ; $i < count($nom_photo); $i++)
		            	{
						    echo '<li data-target="#carousel" data-slide-to="'.$i.'"></li>';
						}?>
					</ul>
					<div> 
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
			<div style="padding:2em;" class="col-lg-8 col-md-8 col-sm-12">
			<?php 
				echo'<h1>'.$nom_item.'<br></h1>';
				
				if($categorie == "Ferraille_tresor")
					echo "Catégorie : Ferraille ou trésor. <br>";

				if($categorie == "VIP")
					echo "Catégorie : Accessoires VIP. <br>";

				if($categorie == "Musee")
					echo "Catégorie : Bon pour le Musée. <br>";

				echo "Description de l'article : <br>".$description."<br>";
				
				if (strpos($ID_type_vente, "achat_immediat") !== FALSE || strpos($ID_type_vente, "offre") !== FALSE)
					echo "Prix : ".$prix."€<br>";
					//Il s'affiche que quand c'est achat immédiat ou offre

				echo '<form action="" method="post">';
				if (strpos($ID_type_vente, "achat_immediat") !== FALSE)
					echo "Achat immédiat possible. <br>";
				//Si l'objet peut être vendu en achat immediat
				if (strpos($ID_type_vente, "achat_immediat") !== FALSE)
				{
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
				{
					echo '<td><input type="number" name="votre_prix_offre" placeholder="Votre offre"></td>';
					if (mysqli_num_rows($resultOffre) != 0)
						echo '<p>Le prix actuel est de '.$prix_vendeur.', veuillez mettre un prix inférieur au prix actuel si vous souhaitez négocier</p>';
					else 
						echo '<p>Le prix actuel est de '.$prix.', veuillez mettre un prix inférieur au prix actuel si vous souhaitez négocier</p>';

					
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
					echo "Les enchères sont ouvertes ! <br>";
					else
						echo "Les enchères sont fermées ! <br>";
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