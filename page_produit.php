<?php
	// On prolonge la session
	//LE DESIGN EST A REVOIR
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
	    $sql = "SELECT * from meilleur_offre WHERE ID_item = $ID_temporaire_item AND ID_acheteur LIKE '$ID_temporaire_acheteur' AND ID_vendeur LIKE '$ID_vendeur'";
	    $result = mysqli_query($db_handle, $sql);
	    if (mysqli_num_rows($result) != 0){
			while ($data = mysqli_fetch_assoc($result)) 
			{
				$prix = $data['Prix_vendeur'];
				$prix_client = $data['Prix_acheteur'];
				$tenta = $data['Tentative'];
				$stat = $data['Statut'];
			}
			
		}
	    //SI l'acheteur clique sur un bouton Faire le demande pour meilleur offre
	    $erreurOffre = "";
	    if(isset($_POST["buttonoffre"])){
	    	if (mysqli_num_rows($resultVerif) != 0 && $type_achat != "offre"){
	    		$erreurOffre .= "Erreur, vous ne pouvez pas faire 2 type d'achat pour un même objet<br>";
	    	}

	    	if ($votre_prix_offre < $prix)
	    	{
	    	//Indique que l'item n'a jamais été dans son panier donc l'user peut faire une offre et le mettre dans son panier
		    	if (mysqli_num_rows($resultVerif) == 0)
	            {
		            //insert dans la table ENCHERIR
			    	$sql = "INSERT INTO meilleur_offre (ID_acheteur, ID_vendeur, ID_item, Prix_acheteur, Prix_vendeur, Tentative, Statut) VALUES ('$ID_temporaire_acheteur', '$ID_vendeur', '$ID_temporaire_item', '$votre_prix_offre', '$prix', '1', '2');";
			    	$result = mysqli_query($db_handle, $sql);
			    	//insert dans la table PANIER
			    	$sql2 = "INSERT INTO panier (ID, ID_item, ID_type_vente) VALUES ('$ID_temporaire_acheteur', '$ID_temporaire_item', 'offre');";
			    	$result2 = mysqli_query($db_handle, $sql2);
			    	//variable test pour blindage saisit enchere inferieur
			    	$erreurOffre .= "Merci de votre demande, nous la transmettrons au vendeur. S'il l'accepte, vous pourrez acheter le produit, sinon faites une meilleure offre.<br>";

	            }elseif($type_achat == "offre"){ //on vérifie si l'objet dans le panier est un achat en offre
			    	//Partie Deuxième ou nième <= 5 offre et que c'est son tour: 
			    	if ($tenta < 5 && $stat == 1){
			    		//L'user a entré un nouveau prix :
			    		$tenta++;
			    		$sql3 = "UPDATE meilleur_offre SET Prix_acheteur = '$votre_prix_offre' , Statut = '2', Tentative = '$tenta' WHERE ID_acheteur = '$ID_temporaire_acheteur' AND ID_vendeur = '$ID_vendeur' AND ID_item = '$ID_temporaire_item';";
						$result3 = mysqli_query($db_handle, $sql3);
			    	}
			    	if ($tenta == 5 && $stat == 1)
			    		$erreurOffre .= "Vous ne pouvez plus faire de tentative, le vendeur vous a répondu, Accecptez ou refusez dans le panier.<br>";
			    	if($tenta == 5 && $stat == 2)
						$erreurOffre .= "Vous avez atteint le nombre limite de demande, vous ne pouvez plus faire de demande ! Attendez la réponse du vendeur.<br>";
			    	if ($stat == 2 && $tenta != 5) //si tenta = 5 c'est le msg au dessus
			    		$erreurOffre .= "Patientez, la demande d'offre précédente n'a pas encore eu de réponse de la part du vendeur. Votre précédente offre est de ".$prix_client." euros <br>";
			    	if ($stat == 3)
						$erreurOffre .= "Le vendeur a accepté pour votre offre au prix de ".$prix_client." euros. Veuillez vous dirigez au panier pour régler.<br>";
					if ($stat == 4)
						$erreurOffre .="Nous sommes navrés de vous annoncer que le produit a été vendu à un autre client.<br>";

			    }
			}else{
				$erreurOffre .= "Erreur, vous ne pouvez pas mettre un prix supérieur au prix actuel.<br>";
			}
	    }
	}
	 else 
	 {
	      echo "Database not found";
	 } 
	 

	// On teste si la variable de session existe et contient une valeur
	if(isset($_SESSION['login']))
	{
		
		$login = $_SESSION['login'];
		$psw = $_SESSION['psw'];
		$statut = $_SESSION['Statut'];
	}

	else 
	{
	  // Si inexistante ou nulle, on redirige vers le formulaire de login
	  // header('Location: http://localhost/Ebay-ece/connexion.php');
	  // exit();
	}

	
?>
<!DOCTYPE html> 
<html> 
	<head>  
		<title>Vendre</title>  
		<meta charset="utf-8">  
		
		<meta name="viewport" content="width=device-width, initial-scale=1">     
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">            
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>  
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>  

		<link rel="stylesheet" type="text/css" href="style.css"> 
		
		<script type="text/javascript">      
			$(document).ready(function() {           
				$('.header').height($(window).height()); 
			}); 
		</script> 

		<script src="https://kit.fontawesome.com/58c71aba33.js" crossorigin="anonymous"></script>
	</head> 	
	<body> 
		<nav class="navbar navbar-expand-md fixed-top"> 
			<button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#main-navigation">    
				<span class="navbar-toggler-icon"></span>       
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
						    <a class="nav-link dropdown-item" href="achat.php" id="l3">Achat</a>
						    <a class="nav-link dropdown-item" href="vendre.php" id="l2">Vendre</a>
						    <a class="nav-link dropdown-item" href="votre_compte.php" id="l1">Admin</a>
					  	</div>
					</li>  
					<li class="nav-item dropdown">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" id="btnpop"><i class="fas fa-user"></i></button>
					  	<div class="dropdown-content border rounded" id="apop1">
						    <a class="nav-link dropdown-item" href="votre_compte.php">Mon compte</a>
						    <a class="nav-link dropdown-item" href="deconnexion.php">Se déconnecter</a>
					  	</div>
					  	<div class="dropdown-content border rounded" id="apop2">
						    <a class="nav-link dropdown-item" href="connexion.php">Se connecter</a>
					  	</div>
					</li> 
					<li class="nav-item">
						<a class="nav-link" href="panier.php"><i class="fas fa-shopping-cart"></i></a>
					</li>    
				</ul>      
			</div> 
		</nav>

		<br><br><br><br>
		
		<div class="container-fluid features" id="con-insc">
            <h1 class="text-center"><?php echo $nom_item."<br>"; ?></h1>
            <?php
            	for ($i = 0 ; $i < count($nom_photo); $i++)
					echo '<img src = "images_web/'.$nom_photo[$i].'" ><br>';
				echo $nom_item."<br>";
				echo "Catégorie : ".$categorie."<br>";
				echo "Vendu par ".$ID_vendeur."<br>";
				echo "Description : ".$description."<br>";
				echo "vidéo :".$video."<br>";
				echo "$ID_type_vente"."<br>";
				if (strpos($ID_type_vente, "achat_immediat") !== FALSE || strpos($ID_type_vente, "offre") !== FALSE)
				{
					echo "Prix : ".$prix."<br>";
				}	//Il s'affiche que quand c'est achat immédiat ou offre
				echo $video."<br>"; // à caché si pas de vidéo

				echo '<form action="" method="post">';
				//Si l'objet peut être vendu en achat immediat
				if (strpos($ID_type_vente, "achat_immediat") !== FALSE)
				{
					echo '<input class="btn border btn-outline-secondary rounded-lg" name="buttonachat" type="submit" value="Ajouter dans le panier" onclick="montrerErreur()"><br><br>';
					if ($erreurAchat != ""){
						echo $erreurAchat;
						$erreurAchat = "";
					}
				}

				//Si l'objet peut être vendu en enchère
				//enchere formulaire
				if (strpos($ID_type_vente, "enchere") !== FALSE)
				{
					echo "Début : ".$Date_debut." à ".$Heure_debut."<br>";
					echo "Fin : ".$Date_fin." à ".$Heure_fin."<br>";
					echo '<td><input type="number" name="votre_prix" placeholder="Votre prix"></td>';
					echo '<p>Le prix actuel est de '.$Prixactuelle.', veuillez mettre un prix supérieur au prix actuel</p>';
					if ($erreurEnchere != ""){
						echo $erreurEnchere;
						$erreurEnchere = "";
					}
					echo '<input class="btn border btn-outline-secondary rounded-lg" name="buttonenchere" type="submit" value="Enchérir">';
				}
				//Si l'objet peut être vendu en offre
				//meilleur offre formulaire
				if (strpos($ID_type_vente, "offre") !== FALSE)
				{
					echo '<td><input type="number" name="votre_prix_offre" placeholder="Votre offre"></td>';
					echo '<p>Le prix actuel est de '.$prix.', veuillez mettre un prix inférieur au prix actuel si vous souhaitez négocier</p>';
					if ($erreurOffre != ""){
						echo $erreurOffre;
						$erreurOffre = "";
					}
					echo '<input class="btn border btn-outline-secondary rounded-lg" name="buttonoffre" type="submit" value="Faire la demande">';
				}
				echo "</form>";
            ?>
        </div>

        <br><br><br>

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
						<a href="achat.php" id="l3"><h5 class="text-uppercase font-weight-bold">Achat</h5></a>
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
								<h5 class="text-uppercase font-weight-bold"> <a href="vendre.php" id="l2">Vendre</a> </h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"> <a href="votre_compte.php">Votre compte</a> </h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"> <a href="admin.php" id="l1">Admin</a> </h5>
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

		<?php
			if(empty($_SESSION['login']) || empty($_SESSION['psw'])) 
			{?>
				<script>
					// Get the button, and when the user clicks on it, execute myFunction
					document.getElementById("btnpop").onclick = function() {montrer()};

					/* myFunction toggles between adding and removing the show class, which is used to hide and show the dropdown content */
					function montrer() 
					{
						document.getElementById("apop2").classList.toggle("show");
					}
				</script> 
			<?php exit();
			}
			else
			{?>
				<script>
					// Get the button, and when the user clicks on it, execute myFunction
					document.getElementById("btnpop").onclick = function() {montrer()};

					/* myFunction toggles between adding and removing the show class, which is used to hide and show the dropdown content */
					function montrer() 
					{
						document.getElementById("apop1").classList.toggle("show");
					}
				</script> <?php
				exit();
			}?>
	</body> 
</html> 