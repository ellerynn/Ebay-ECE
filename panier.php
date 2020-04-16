<?php
	include("const.php");
	// On prolonge la session
	session_start();
	$ID_temporaire_acheteur = 29;
	//declaration
	$table_item = array();
	$table_photo = array();

	$nom_item = array();
	$ID_vendeur = array();
	$description = array();
	$categorie = array();
	$prix = array();
	$video = array();
	$ID =array();
	$ID_item = array();
	$ID_type_vente = array();

	if(isset($_SESSION['login']))
	{
		$login = $_SESSION['login'];
		$psw = $_SESSION['psw'];
		$statut = $_SESSION['Statut'];
		$id = $_SESSION['ID'];
	}
	// On teste si la variable de session existe et contient une valeur
	else
	{
	  // Si inexistante ou nulle, on redirige vers le formulaire de login
	  //header('Location: connexion.php');
	  exit();
	}
	$database = "ebay ece paris";
	$db_handle = mysqli_connect('localhost', 'root', '');
	$db_found = mysqli_select_db($db_handle, $database);
	if ($db_found) 
	{
		$sql = "SELECT * FROM panier WHERE ID LIKE '$ID_temporaire_acheteur' AND ID_type_vente LIKE 'achat_immediat'";
		$result = mysqli_query($db_handle, $sql);

			if (mysqli_num_rows($result) == 0) {
				//Livre inexistant
				echo "Erreur, cet item n'est pas disponible. <br>";
			} 
			else {
				$i=0;
				while ($data = mysqli_fetch_assoc($result)) 
				{
					$ID[$i] = $data['ID'];
					$ID_item[$i] = $data['ID_item'];
					$ID_type_vente[$i] = $data['ID_type_vente'];
					$i++;
				}
				
			}


		//On recupère les données de chaque item de la table Item
		for($a=0; $a < count($ID_item); $a++){
		$sql1 = "SELECT * FROM item WHERE ID_item LIKE '$ID_item[$a]' "; //retrouver les ID_items issu de ID_item[]
		$result1 = mysqli_query($db_handle, $sql1);

			if (mysqli_num_rows($result1) == 0) {
				echo "Erreur, cet item n'est pas disponible. <br>";
			}
			else {
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

					$table_item["$ID_item[$a]"] = $temp; // Tableau associatif
				}
				
			}

		}	

		for($a=0; $a < count($ID_item); $a++){
		$sql1 = "SELECT * FROM photo WHERE ID_item LIKE '$ID_item[$a]' ";
		$result1 = mysqli_query($db_handle, $sql1);
			if (mysqli_num_rows($result1) == 0) {
				//Livre inexistant
				echo "Erreur, cet item n'est pas disponible. <br>";
			} 
			else {
				$v = 0;
				$temp =array();
				while ($data = mysqli_fetch_assoc($result1) )  //extraction de toute les photos d'un item donnée ($u)
				{
					$temp[$v] = $data['Nom_photo'];
					$v++;
				}
				$table_photo["$ID_item[$a]"]= $temp; //array de photo dans tableau associatif
				
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
						<a class="nav-link" href="panier.php"><i class="fas fa-shopping-cart"></i></a>
					</li>    
				</ul>      
			</div> 
		</nav>	

		<div class="container-fluid features" id="con-insc">
			<h1 class="text-center"> Votre panier</h1>
			<?php
			$prix_tot_achat = "0";
			for ($i = 0 ; $i<count($ID_item); $i++){ //La taille du tableau ID_acheteur est pareil que le tableau ID_item 
				//Affichage des images pour un item donnée :
				//traitement de la table photo
				for ($u = 0 ; $u < count($table_photo["$ID_item[$i]"]); $u++){
					echo '<img src = "images_web/'.$table_photo["$ID_item[$i]"][$u].'" height=100 width =100 ><br>';

				}
				//traitement de la table item:
				echo "L'ID de l'item :".$table_item["$ID_item[$i]"][0]."<br>";
				echo "Le nom de l'item :".$table_item["$ID_item[$i]"][1]."<br>";
				echo "Le vendeur de l'item :".$table_item["$ID_item[$i]"][2]."<br>"; 
				echo "La description de l'item :".$table_item["$ID_item[$i]"][4]."<br>"; //3 = le type de vente mais on en veut pas 
				echo "Le catégorie de l'item :".$table_item["$ID_item[$i]"][5]."<br>"; 
				echo "Le Prix de l'item :".$table_item["$ID_item[$i]"][6]."<br>"; 
				echo "La video de l'item :".$table_item["$ID_item[$i]"][7]."<br><br>"; //Faudrait le lien mais là on a affiché que le nom
				$prix_tot_achat+=$table_item["$ID_item[$i]"][6];
			}

			 
			echo "Prix total svp: ".$prix_tot_achat;
			?>
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