<?php
	include("const.php");
	// On prolonge la session
	session_start();
	// On teste si la variable de session existe et contient une valeur
	if(isset($_SESSION['login']))
	{
		$login = $_SESSION['login'];
		$psw = $_SESSION['psw'];
		$statut = $_SESSION['Statut'];
		$id = $_SESSION['ID'];
	}
	else
	{
	  // Si inexistante ou nulle, on redirige vers le panier
	  header('Location: connexion.php');
	  exit();
	}

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

	$ID_temporaire_acheteur = $id;
	$supprimer = array();
	$supprimer2 = array();
	$accepter_offre = array();

	//declaration achat immediat
	$table_item = array(); //Achat immédiat
	$table_photo = array();
	$vendeurItems =array();
	$pseudoVendeur = array();
	$ID_item = array();

	 // declaration meilleur offre
	$table_item2 = array();
	$table_photo2 = array();
	$ID_item2 = array();	//Offre
	$statut2 = array();
	$prix_acheteur_accepte = array();
	$prix_vendeur = array();

	// declaration enchere
	$table_item3 = array();
	$table_item4 = array();
	$table_photo3 = array();

	if(isset($_SESSION['login']))
	{
		$login = $_SESSION['login'];
		$psw = $_SESSION['psw'];
		$statut = $_SESSION['Statut'];
		$id = $_SESSION['ID'];
	}

	$database = "ebay ece paris";
	$db_handle = mysqli_connect('localhost', 'root', '');
	$db_found = mysqli_select_db($db_handle, $database);
	if ($db_found) 
	{
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
				if ($itemDateFin < $date_actuelle) 
				{	//Si la date de fin de l'enchère est passé
					$sqlModif = "UPDATE liste_enchere SET Fin = 1 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
				}
				if ($itemDateFin == $date_actuelle && $itemHeureFin <= $heure_actuelle)
				{	//Si la date de fin de l'enchère est passé
					$sqlModif = "UPDATE liste_enchere SET Fin = 1 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
				}
				if ($itemDateDebut > $date_actuelle)
				{	//Si pas encore ouvert
					$sqlModif = "UPDATE liste_enchere SET Fin = 2 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
				}
				if ($itemDateDebut == $date_actuelle && $itemHeureDebut > $heure_actuelle)
				{	//Si pas encore ouvert
					$sqlModif = "UPDATE liste_enchere SET Fin = 2 WHERE ID_item = $tempItem;";
					$resultModif = mysqli_query($db_handle,$sqlModif);
				}
			}
		}

		//On récupère tout les items de type achat_immédiat de l'acheteur dans la table panier
		$sql = "SELECT ID_item FROM panier WHERE ID LIKE '$ID_temporaire_acheteur'";
		$result = mysqli_query($db_handle, $sql);
		if (mysqli_num_rows($result) != 0) 
		{	//S'il existe
			while ($data = mysqli_fetch_assoc($result)) 
			{	//On récupère l'id de l'item
				$temp = $data['ID_item'];
				$sql1 = "SELECT ID_vendeur FROM item WHERE ID_item = $temp"; //On cherche le vendeur
				$result1 = mysqli_query($db_handle, $sql1);
				if (mysqli_num_rows($result1) != 0) 
				{	
					while ($data = mysqli_fetch_assoc($result1)) 
					{	//On récupère l'id du vendeur de cet item
						$temp2 = $data['ID_vendeur'];
						$sql2 = "SELECT Pseudo FROM vendeur WHERE ID = $temp2";	//On cherche le pseudo du vendeur
						$result2 = mysqli_query($db_handle, $sql2);
						if (mysqli_num_rows($result2) != 0) 
						{	//Si c'est un vendeur
							while ($data = mysqli_fetch_assoc($result2)) 
							{	//On enregistre dans un tableau associatif
								$vendeurItems["$temp"] ="Vendeur: ".$data['Pseudo']; // {Clée ID_item => Pseudo}
							}
						}
						else
						{	//Si c'est un administrateur
							$sql3 = "SELECT Prenom FROM personne WHERE ID = $temp2";
							$result3 = mysqli_query($db_handle, $sql3);
							if (mysqli_num_rows($result3) != 0) 
							{	
								while ($data = mysqli_fetch_assoc($result3)) 
								{	//On récupère son prénom
									$vendeurItems["$temp"] = "Admin: ".$data['Prenom']; // {Clé ID_item => Prenom}
								}
							}
						}
					}
				}
			}
		}

		//PARTIE RECUPERATION des données des items de type d'achat ACHAT IMMEDIAT du panier de l'user
		$sql = "SELECT * FROM panier WHERE ID LIKE '$ID_temporaire_acheteur' AND ID_type_vente LIKE 'achat_immediat'";
		$result = mysqli_query($db_handle, $sql);
		if (mysqli_num_rows($result) != 0) 
		{
			$i=0;
			while ($data = mysqli_fetch_assoc($result)) 
			{
				$ID_item[$i] = $data['ID_item']; //{à la ième case se trouve Id de l'item de type achat_im}
				$i++;
			}
			
		}
		//On recupère les données de chaque item de type achat immédiat de la table Item
		for($a=0; $a < count($ID_item); $a++)
		{
			$sql1 = "SELECT * FROM item WHERE ID_item LIKE '$ID_item[$a]' "; //retrouver les ID_items issu de ID_item[]
			$result1 = mysqli_query($db_handle, $sql1);

			if (mysqli_num_rows($result1) != 0) 
			{
				$i=0;
				$temp = array();
				while ($data = mysqli_fetch_assoc($result1) ) 
				{
					$i_temp = 0;
					$temp[$i_temp] = $ID_item[$a]; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
					$i_temp++;
					$temp[$i_temp] = $data['Nom_item']; // i_temp = 1
					$i_temp++;
					$temp[$i_temp] = $data['ID_vendeur']; // i_temp = 2
					$i_temp++;
					$temp[$i_temp] = $data['ID_type_vente']; // i_temp = 3
					$i_temp++;
					$temp[$i_temp] = $data['Description']; // i_temp = 4
					$i_temp++;
					$temp[$i_temp] = $data['Categorie']; // i_temp = 5
					$i_temp++;
					$temp[$i_temp] = $data['Prix']; // i_temp = 6
					$i_temp++;
					$temp[$i_temp] = $data['Video']; // i_temp = 7

					$table_item["$ID_item[$a]"] = $temp; // {Clée Id de l'item => array de donnée de l'item}
				}
				
			}
			//Ses photos également
			$sql1 = "SELECT * FROM photo WHERE ID_item LIKE '$ID_item[$a]' ";
			$result1 = mysqli_query($db_handle, $sql1);
			if (mysqli_num_rows($result1) != 0) 
			{
				$v = 0;
				$temp =array();
				while ($data = mysqli_fetch_assoc($result1) )  //extraction de toute les photos d'un item donnée ($u)
				{
					$temp[$v] = $data['Nom_photo'];
					$v++;
				}
				$table_photo["$ID_item[$a]"]= $temp; // {Id item => array de photo de l'item}
			}

		}	
		//FIN récup pour les items d'achat immédiat

		//PARTIE RECUPERATION des items de type d'achat MEILLEUR OFFRE du panier de l'user
		$sql1 = "SELECT * FROM panier WHERE ID LIKE '$ID_temporaire_acheteur' AND ID_type_vente LIKE 'offre'";
		$result1 = mysqli_query($db_handle, $sql1);
		if (mysqli_num_rows($result1) != 0) {
			$i=0;
			while ($data = mysqli_fetch_assoc($result1)) 
			{
				$ID_item2[$i] = $data['ID_item']; //{à la ième case se trouve Id de l'item de type offre}
				$i++;
			}
		}
		//On recupère les données de chaque item de type offre de la table Item
		for($a=0; $a < count($ID_item2); $a++)
		{	
			$sql1 = "SELECT * FROM item WHERE ID_item LIKE '$ID_item2[$a]' ";
			$result1 = mysqli_query($db_handle, $sql1);

			if (mysqli_num_rows($result1) != 0) 
			{
				$i=0;
				$temp2 = array();
				while ($data = mysqli_fetch_assoc($result1)) 
				{
					$i_temp2 = 0;
					$temp2[$i_temp2] = $ID_item2[$a]; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
					$i_temp2++;
					$temp2[$i_temp2] = $data['Nom_item']; // i_temp = 1
					$i_temp2++;
					$temp2[$i_temp2] = $data['ID_vendeur']; // i_temp = 2
					$i_temp2++;
					$temp2[$i_temp2] = $data['ID_type_vente']; // i_temp = 3
					$i_temp2++;
					$temp2[$i_temp2] = $data['Description']; // i_temp = 4
					$i_temp2++;
					$temp2[$i_temp2] = $data['Categorie']; // i_temp = 5
					$i_temp2++;
					$temp2[$i_temp2] = $data['Prix']; // i_temp = 6
					$i_temp2++;
					$temp2[$i_temp2] = $data['Video']; // i_temp = 7

					$table_item2["$ID_item2[$a]"] = $temp2; // {Id item => array de donnée de l'item}

				}
			}

			$sql1 = "SELECT * FROM photo WHERE ID_item LIKE '$ID_item2[$a]' ";
			$result1 = mysqli_query($db_handle, $sql1);
			if (mysqli_num_rows($result1) != 0) 
			{
				$v = 0;
				$temp2 =array();
				while ($data = mysqli_fetch_assoc($result1) )  //extraction de toute les photos d'un item donnée ($u)
				{
					$temp2[$v] = $data['Nom_photo'];
					$v++;
				}
				$table_photo2["$ID_item2[$a]"]= $temp2; //{Id item => array de photo de l'item}
				
			}
		}

		//on recupere les données dans meilleur offre des offres que l'user à réaliser 
		$sql3 = "SELECT * FROM meilleur_offre WHERE ID_acheteur LIKE '$ID_temporaire_acheteur'";
		$result3 = mysqli_query($db_handle, $sql3);
		if (mysqli_num_rows($result3) != 0) 
		{
			$i=0;
			while ($data = mysqli_fetch_assoc($result3)) 
			{
				$dat = $data['ID_item'];
				$statut2["$dat"] = $data['Statut']; // statue2 {ID_item => status }
				$prix_acheteur_accepte["$dat"] = $data['Prix_acheteur']; // {ID_item => Prix_acheteur}
				$prix_vendeur["$dat"] = $data['Prix_vendeur']; // {Id_item => Prix_vendeur}
				$i++;
			}	
		}
		//FIN RECUPERATION item pour offre

		//ENCHERE
		$table_encherir = array();
		//récupération de tout les enchères faites par le client
		$sql5 = "SELECT * FROM encherir WHERE ID_acheteur LIKE '$ID_temporaire_acheteur'";
		$result5 = mysqli_query($db_handle, $sql5);
		if (mysqli_num_rows($result5) != 0) 
		{
			$i_temp4=0;
			$i = 0;
			$temp4 = array();
			while ($data = mysqli_fetch_assoc($result5)) 
			{
				$i_temp4 = 0;
				$temp4[$i_temp4] = $data['ID_item']; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
				$i_temp4++;
				$temp4[$i_temp4] = $data['ID_enchere']; // i_temp = 1
				$i_temp4++;
				$temp4[$i_temp4] = $data['Prix_acheteur']; // i_temp = 2

				$table_encherir["$i"] = $temp4; //{$ième => données de l'enchérissement faite par le client}
				$i++;
			}	
		}
		//On recupère les données de chaque item enchéri de la table Item
		for($a=0; $a < count($table_encherir); $a++)
		{
			$var = $table_encherir["$a"][0];
			$sql1 = "SELECT * FROM item WHERE ID_item LIKE '$var' "; //retrouver les ID_items issu de ID_item[]
			$result1 = mysqli_query($db_handle, $sql1);

			if (mysqli_num_rows($result1) != 0) 
			{
				$i=0;
				$temp3 = array();
				while ($data = mysqli_fetch_assoc($result1) ) 
				{
					$i_temp3 = 0;
					$temp3[$i_temp3] = $var; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
					$i_temp3++;
					$temp3[$i_temp3] = $data['Nom_item']; // i_temp = 1
					$i_temp3++;
					$temp3[$i_temp3] = $data['ID_vendeur']; // i_temp = 2
					$i_temp3++;
					$temp3[$i_temp3] = $data['ID_type_vente']; // i_temp = 3
					$i_temp3++;
					$temp3[$i_temp3] = $data['Description']; // i_temp = 4
					$i_temp3++;
					$temp3[$i_temp3] = $data['Categorie']; // i_temp = 5
					$i_temp3++;
					$temp3[$i_temp3] = $data['Prix']; // i_temp = 6
					$i_temp3++;
					$temp3[$i_temp3] = $data['Video']; // i_temp = 7
					$var = $table_encherir["$a"][0];
					$table_item3["$var"] = $temp3; // {Id_item => données de l'item de type d'achat enchere de la table item}
				}
			}
			//photos également
			$sql1 = "SELECT * FROM photo WHERE ID_item LIKE '$var'";
			$result1 = mysqli_query($db_handle, $sql1);
			if (mysqli_num_rows($result1) != 0) 
			{
				$v = 0;
				$temp3 =array();
				while ($data = mysqli_fetch_assoc($result1) )  
				{	
					$temp3[$v] = $data['Nom_photo'];
					$v++;
				}
				$table_photo3["$var"]= $temp3; //{Id item => photo de l'item}
				
			}
			$sql1 = "SELECT * FROM liste_enchere WHERE ID_item LIKE '$var' "; //Retrouve données de l'enchère de l'item enchéri par l'user
			$result1 = mysqli_query($db_handle, $sql1);

			if (mysqli_num_rows($result1) != 0) 
			{
				$i=0;
				$temp4 = array();
				while ($data = mysqli_fetch_assoc($result1) ) 
				{
					$i_temp4 = 0;
					$temp4[$i_temp4] = $data['ID_item']; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
					$i_temp4++;
					$temp4[$i_temp4] = $data['Date_fin']; // i_temp = 1
					$i_temp4++;
					$temp4[$i_temp4] = $data['Heure_fin']; // i_temp = 2
					$i_temp4++;
					$temp4[$i_temp4] = $data['Prix_premier']; // i_temp = 3
					$i_temp4++;
					$temp4[$i_temp4] = $data['Prix_second']; // i_temp = 4
					$i_temp4++;
					$temp4[$i_temp4] = $data['Fin']; // i_temp = 5
					$i_temp4++;
					$temp4[$i_temp4] = $data['ID_enchere']; // i_temp = 5
					$table_item4["$var"] = $temp4; //{ID item => donnée de la table liste_enchere}   
				}
			}
		}

	}
	else
	{
		echo "Database not found";
	}
	//fermer la connexion 

	//Si cliqué un lien (nom de l'item dans son panier), envoie ID de l'item
	if (isset($_GET['idLien'])){ // Si un lien en particulier est cliqué : On récupère la valeur de idLien (dedans contien l'id de l'item)
		$sql = "SELECT * from item WHERE ID_item = ".$_GET['idLien'].""; // On vérifie quand même s'il existe dans la BDD
		$result = mysqli_query($db_handle, $sql);	
		if (mysqli_num_rows($result) != 0)
		{ 	//Si l'objet existe, on le stock dans la session et on l'envoi à la page page_produit.php
			$_SESSION['itemClick'] = $_GET['idLien'];
			header('Location: page_produit.php');
			exit();
		}
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

			<form style="display: none;" id="barre" action="rechercher.php" class="navbar-form inline-form">
				<div class="form-group">
				  	<span style="color:white;"><i class="fas fa-search"></i></span>
				   	<input type="search" class="input-sm form-control-sm" placeholder="Rechercher sur eBay ECE">
				   	<button name="chercher" class="btn btn-outline-secondary btn-sm">Chercher</button>
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
						<a class="nav-link" href="panier.php"><i class="fas fa-shopping-cart"></i></a>
					</li>    
				</ul>      
			</div> 
		</nav>	
<br><br>
		<div class="container-fluid features" id="con-insc">
			<h1 class="text-center"> Votre panier</h1>
            <div class="panel border" style="margin: 0 auto; width: 1000px; padding: 50px; margin-bottom: 1em;">

			<?php
			//Variable des prix totals
			$prix_tot_achat = "0"; //Immédiat
			$prix_tot_achat2 = "0";	//En cours
			//Le panier immédiat
			echo'
			<table class = "table center">
				<tr>
					<td class = "text-center"><B>Panier immédiat<B></td>
				</tr>

			</table>';
			//achat immediat
			echo '
			<table class="table">
				<tr>
					<td>Type d\'achat</td>
					<td>Photo</td>
					<td>Nom</td>
					<td>Le vendeur</td>
					<td>La catégorie</td>
					<td>Le prix</td>
					<td>Action</td>
				</tr>';

			for ($i = 0 ; $i<count($ID_item); $i++){ //Pour chaque item de type achat immédiat
				//Affichage images, nom, vendeur, catégorie, prix, action
				$supprimer[$i] = "supprimer_".$i;
		  echo '<tr>
		  			<td>Achat immédiat</td>
		  			<td><img src = "images_web/'.$table_photo["$ID_item[$i]"][0].'" height=100 width =100 ></td>
		  			<td><a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$table_item["$ID_item[$i]"][0].'">'.$table_item["$ID_item[$i]"][1].'</a></td>';

		  			//<td>'.$table_item["$ID_item[$i]"][2].'</td>
		  			echo'
		  			<td>'.$vendeurItems["$ID_item[$i]"].'</td>
		  			<td>'.$table_item["$ID_item[$i]"][5].'</td>
		  			<td>'.$table_item["$ID_item[$i]"][6].'€</td>
		  			<td>
		  				<form action="" method="post">
		  					<input class="btn border btn-outline-secondary rounded-lg" name="'.$supprimer[$i].'" type="submit" value="Supprimer l\'item du panier">
		  				</form>
		  			</td>
		  		</tr>';
				$prix_tot_achat+=$table_item["$ID_item[$i]"][6]; //ajout au prix total à régler
			}

			for ($i = 0 ; $i<count($ID_item); $i++)//Pour chaque item de type achat_immédiat
			if (isset($_POST["$supprimer[$i]"]))  //Si le bouton donné est cliqué
			{
				$stock1 = $table_item["$ID_item[$i]"][0]; //ID de l'item
				$stock2 = $table_item["$ID_item[$i]"][2]; //Type d'achat de l'item réalisé par l'user
				$sql = "DELETE FROM panier WHERE ID_item = $stock1 AND ID = $ID_temporaire_acheteur AND ID_type_vente = 'achat_immediat'";
				$result = mysqli_query($db_handle, $sql);
				$prix_tot_achat-=$table_item["$ID_item[$i]"][6]; //on soustrait du prix total à régler
				//Rafraichissement de la page
				echo "<script type='text/javascript'>document.location.replace('panier.php');</script>";
				exit();
			}

			//meilleur offre immediat
			for ($i = 0 ; $i<count($ID_item2); $i++){ //nombre d'objets en offre issu du panier de l'acheteur
				//Affichage images, nom, vendeur, catégorie, prix, action
				if($statut2["$ID_item2[$i]"] == 3){ // Statut 3 = accepté par le vendeur
			echo '
				<tr>
					<td>Offre conclu</td>
					<td><img src = "images_web/'.$table_photo2["$ID_item2[$i]"][0].'" height=100 width =100 ></td>
					<td><a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$table_item2["$ID_item2[$i]"][0].'">'.$table_item2["$ID_item2[$i]"][1].'</a></td>
		  			<td>'.$vendeurItems["$ID_item2[$i]"].'</td>
					<td>'.$table_item2["$ID_item2[$i]"][5].'</td>
					<td>'.$prix_acheteur_accepte["$ID_item2[$i]"].'€</td>
					<td>Aucune Modification possible</td>
				</tr>';
					$prix_tot_achat+=$prix_acheteur_accepte["$ID_item2[$i]"]; //ajout au prix total à régler
				}
			}	
			//Enchere achat possible
			for ($i = 0 ; $i< count($table_encherir); $i++) //nombre item en enchéri par l'user
			{ //Affichage images, nom, vendeur, catégorie, prix, action
				$var = $table_encherir["$i"][0]; // ID de l'item
				if($table_item4["$var"][5] == 1 && $table_item4["$var"][3] == $table_encherir["$i"][2])
				{//Si l'enchère est terminé et lui gagnant (en comparant le prix le plus élevé à son enchérrismt) 
					$prixEnch = $table_item4["$var"][4]+1; // Second + 1 = Prix qui doit payer
					echo '
				<tr>
					<td>Enchère gagnée <br>Fin : '.$table_item4["$var"][1].' à '.$table_item4["$var"][2].'</td>
					<td><img src = "images_web/'.$table_photo3["$var"][0].'" height=100 width =100 ></td>
					<td><a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$table_item3["$var"][0].'">'.$table_item3["$var"][1].'</a></td>
		  			<td>'.$vendeurItems["$var"].'</td>
					<td>'.$table_item3["$var"][5].'</td> 
					<td>'.$prixEnch.'€</td>
					<td>Aucune Modification possible</td>
				</tr>'; 
					//Ajout au prix total à régler
					$prix_tot_achat+=$table_item4["$var"][4];
					$prix_tot_achat++;
				}
			}	
				//On stock dans session, si il régle son panier
				$_SESSION["prix_total"] = $prix_tot_achat;
		echo '	<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>Total: </td>
					<td>'.$prix_tot_achat.'€</td>
					<td><a type="button" class="btn btn-secondary" href="paiement.php">Passer au paiement</a></td>
				</tr>';
			echo "</table>"; 

//Le panier EN COURS
			echo '
			<table class = "table center">
				<tr>
					<td class = "text-center"><B>Panier en attente<B></td>
				</tr>

			</table>';
			//meilleur offre en cours
			echo '
			<table class="table">
				<tr>
					<td>Type d\'achat</td>
					<td>Photo</td>
					<td>Nom</td>
					<td>Le vendeur</td>
					<td>La catégorie</td>
					<td>Le prix</td>
					<td>Action</td>
				</tr>';
			for ($i = 0 ; $i<count($ID_item2); $i++) //pour chaque item de l'user par offre
			{
			//Affichage images, nom, vendeur, catégorie, le prix, les acions
				if($statut2["$ID_item2[$i]"] <= 2)
				{		//Affichage de tout les items dont l'offre est au statut 1 ou 2
						//Boutons 
						$accepter_offre["$ID_item2[$i]"] = "accepter_offre_".$i;
						$supprimer2["$ID_item2[$i]"] = "supprimer2_".$i;

					echo'
					<tr>';
					if ($statut2["$ID_item2[$i]"] == 1)
						echo '<td>Offre en cours<br> Votre tour</td>';
					if ($statut2["$ID_item2[$i]"] == 2)
						echo '<td>Offre en cours<br> En attente de la réponse du vendeur</td>';
					echo '
						<td><img src = "images_web/'.$table_photo2["$ID_item2[$i]"][0].'" height=100 width =100 ></td>
						<td><a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$table_item2["$ID_item2[$i]"][0].'">'.$table_item2["$ID_item2[$i]"][1].'</a></td>
		  				<td>'.$vendeurItems["$ID_item2[$i]"].'</td>
						<td>'.$table_item2["$ID_item2[$i]"][5].'</td>
						<td>Votre offre: '.$prix_acheteur_accepte["$ID_item2[$i]"].'€<br>
							Vendeur: '.$prix_vendeur["$ID_item2[$i]"].'€</td>
						<td>
							<form action="" method="post">
								<input class="btn border btn-outline-secondary rounded-lg" name="'.$accepter_offre["$ID_item2[$i]"].'" type="submit" value="Accepter l\'offre"><br>
								<input class="btn border btn-outline-secondary rounded-lg" name="'.$supprimer2["$ID_item2[$i]"].'" type="submit" value="Supprimer l\'item du panier">
							</form>
						</td>
					</tr>';
						$prix_tot_achat2+=$prix_acheteur_accepte["$ID_item2[$i]"]; //Ajout au prix en cours
					}	
			}	 
			for ($i = 0 ; $i<count($ID_item2); $i++)
			{ //on parcourt l'ensemble des items de type offre
				$var = $ID_item2[$i];
				if ($statut2["$var"] <= 2)
				{ //peut supprimer quand statut = 1 ou 2
					if (isset($_POST["$supprimer2[$var]"])) 
					{	//on suprimme du panier et dans meilleur offre statut = 5 = il a supprimé
						$stock1 = $table_item2["$ID_item2[$i]"][0];
						$stock2 = $table_item2["$ID_item2[$i]"][2];
						$sql = "DELETE FROM panier WHERE ID_item = $stock1 AND ID = $ID_temporaire_acheteur AND ID_type_vente = 'offre'";
						$result = mysqli_query($db_handle, $sql);
						$sql2 = "UPDATE meilleur_offre SET Statut = '5' WHERE ID_item = $stock1 AND ID_acheteur = $ID_temporaire_acheteur AND ID_vendeur = $stock2";
						$result2 = mysqli_query($db_handle, $sql2);
						$prix_tot_achat2-=$table_item2["$ID_item2[$i]"][6];
						echo "<script type='text/javascript'>document.location.replace('panier.php');</script>";
						exit();
					}
				}
				if ($statut2["$var"] == 1)
				{	//on peut accepté que quand statut = 1 (au tour du client) si = 2, même si tu cliques ça ne 		fera rien
					if (isset($_POST["$accepter_offre[$var]"]))
					{
						$stock1 = $table_item2["$ID_item2[$i]"][0];
						$stock2 = $table_item2["$ID_item2[$i]"][2];
						$sql = "UPDATE meilleur_offre SET Statut = '3' WHERE ID_item = $stock1 AND ID_acheteur = $ID_temporaire_acheteur AND ID_vendeur = $stock2";
						$result = mysqli_query($db_handle, $sql);
						$prix_tot_achat2-=$table_item2["$ID_item2[$i]"][6];
						echo "<script type='text/javascript'>document.location.replace('panier.php');</script>";
						exit();
					}
					else
						$prix_tot_achat2+=$table_item2["$ID_item2[$i]"][6];
				}
			}

			//Enchere achat non possible
			for ($i = 0 ; $i<count($table_encherir); $i++) //Pour chaque item enchéri par l'user
			{ 
				////Affichage de tout les items dont l'offre est au statut 1 ou 2
				$var = $table_encherir["$i"][0];
				if($table_item4["$var"][5] == 0)
				{
echo'
					<tr>
						<td>Enchère en cours <br>Fin : '.$table_item4["$var"][1].' à '.$table_item4["$var"][2].'</td>
						<td><img src = "images_web/'.$table_photo3["$var"][0].'" height=100 width =100 ></td>
						<td><a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$table_item3["$var"][0].'">'.$table_item3["$var"][1].'</a></td>
		  				<td>'.$vendeurItems["$var"].'</td>
						<td>'.$table_item3["$var"][5].'</td>
						<td>Votre mise : '.$table_encherir["$i"][2].'€<br>
							Montant actuel : '.$table_item4["$var"][3].'€</td>
						<td><a type="button" class="btn btn-secondary" href="paiement.php">Passer au paiement</a></td>
					</tr>';
					$prix_tot_achat2+=$table_encherir["$i"][2]; //ajout de son prix dans prix potentiel à réglé
				}
			}
			echo '	<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>Estimation total: </td>
					<td>'.$prix_tot_achat2.'</td>
					<td></td>
				</tr>';
			echo "</table>";

			mysqli_close($db_handle);
			?>
</div>
		</div>


		<!--Créer un pied de page (footer)-->
		<footer class="page-footer">   
			<div class="container">    
				<div class="row">       
					<div class="col-lg-3 col-md-3 col-sm-12">	
						<h5 class="text-uppercase font-weight-bold">Catégories</h5>
						<ul>  
							<li>
								<a href="#">Ferraille ou Trésor</a>
							</li>    
							<li>
								<a href="#">Bon pour le Musée</a>
							</li> 
							<li>
								<a href="#">Accessoires VIP</a>
							</li>               
						</ul> 
					</div> 
					<div class="col-lg-3 col-md-3 col-sm-12">	
						<a href="achat.php"><h5 class="text-uppercase font-weight-bold">Achat</h5></a>
						<ul>  
							<li>
								<a href="#">Enchères</a>
							</li>    
							<li>
								<a href="#">Achetez-le maintenant</a>
							</li> 
							<li>
								<a href="#">Meilleure offre</a>
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
								<h5 class="text-uppercase font-weight-bold">Admin</h5>
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