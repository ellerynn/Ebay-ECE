<?php
	include("const.php");

	// On prolonge la session
	session_start();
	if(isset($_SESSION['itemClick']))
	{
		$item_clique = $_SESSION['itemClick'];
		$login = $_SESSION['login'];
		$psw = $_SESSION['psw'];
		$statut = $_SESSION['Statut'];
		$id = $_SESSION['ID'];
		$ID_temporaire_item = $item_clique;
		$ID_temporaire_acheteur = $id ;
	}
	/*else
	{
	  header('Location: accueil.php');
	  exit();
	}*/

	//identifier votre BDD
    $database = "ebay ece paris";
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);

    $rechercher = isset($_POST["rechercher"])? $_POST["rechercher"] : "";

    $nom_item ="";
	$ID_vendeur = "";
	$ID_type_vente = "";
	$description = "";
	$categorie = "";
	$prix = "";
	$video = "";
	$erreur ="";
	$ID_i = array();
	$ID_i = array();
	$nom = array();
	$prix = array();
	$categorie = array();
	$temp = array();
	$c = 0;

if($db_found)
{
	if (isset($_POST["chercher"])) 
	{

		if ($rechercher == "") 
            $erreur .= "Rechercher est vide. <br>"; 
		//PARTIE AFFICHAGE
		//Récuperation donnee table item
		if($erreur == "")
		{
			$sql = "SELECT * FROM item WHERE Nom_item LIKE '%$rechercher%'";
			$r = mysqli_query($db_handle, $sql);
			if (mysqli_num_rows($r) != 0)
			{
				$i = 0;
				//On récupère id et prix de chaque item
				while ($data = mysqli_fetch_assoc($r)) 
				{
					$ID_i[$i] = $data['ID_item'];
					$prix[$i] = $data['Prix'];
					$nom[$i] = $data['Nom_item'];
					$categorie[$i] = $data['Categorie'];
					$i++;
				}

				//Pour chaque item
				for ($u = 0 ; $u < count($ID_i) ; $u++)
				{ 
					
					$temp[0] = $ID_i[$u]; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
					$temp[1] = $nom[$u];
					$temp[2] = $prix[$u]; 
					$temp[3] = $categorie[$u];

					$table_item["$ID_i[$u]"] = $temp; // Tableau associatif
				}

				//Récuperation de la première photo de chaque item
				for($a=0; $a < count($ID_i); $a++)
				{
					$sql2 = "SELECT * FROM photo WHERE ID_item LIKE '$ID_i[$a]' ";
					$r2 = mysqli_query($db_handle, $sql2);
						
					if (mysqli_num_rows($r) != 0) 
					{
						$data = mysqli_fetch_assoc($r2);
						$photo = $data['Nom_photo'];
						$table_photo["$ID_i[$a]"]= $photo; //array de photo dans tableau associatif
					}
				}
			}
				
		}
	}
	
	if (isset($_GET['idLien'])){ // Si un lien en particulier est cliqué : On récupère la valeur de idLien (dedans ctn l'id de l'item)
		$sql = "SELECT * from item WHERE ID_item = ".$_GET['idLien'].""; // On vérifie quand même s'il existe dans la BDD
		$result = mysqli_query($db_handle, $sql);	
		if (mysqli_num_rows($result) != 0){ //Si l'objet existe, on le stock dans la session et on le renvoi à la page page_produit.php
			$_SESSION['itemClick'] = $_GET['idLien'];
			header('Location: page_produit.php');
			exit();
		}
	}
}
else
	echo "pas de database";
//fermer la connexion
mysqli_close($db_handle); 


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

			<form action="" method="post">
				<div class="form-group">
					<span style="color:white;"><i class="fas fa-search"></i></span>
				    <input type="search" class="input-sm form-control-sm" name = "rechercher" placeholder="Rechercher sur eBay ECE">
				    <button class="btn btn-outline-secondary btn-sm" name = "chercher">Chercher</button>

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
        <!-- AFFICHAGE ITEM EN FONCTION DE LA RECHERCHE-->
        <?php
		//Pour chaque item		
		for ($i = 0 ; $i<count($ID_i); $i++)
		{
			$var = $ID_i[$i];
			if($c == 0)
			{?>
				<div class="row">
					<div class="col-lg-3 col-md-3 col-sm-12"> 
						<div class="img-thumbnail" style="margin:0 auto; height: 250px;width:250px"> <?php
							echo '<img class="img-fluid" src = "images_web/'.$table_photo["$ID_i[$i]"].'" height = 100% width = 100%>';?> 
						</div>
						<?php echo '<td>';
							echo '<a style="margin-left:2em" href = "'.$_SERVER['PHP_SELF'].'?idLien='.$var.'">'.$table_item["$ID_i[$i]"][1].'</a> </td>';  ?>
    				</div> <?php
			}

			if($c == 1 || $c == 2)
			{?>
    				<div class="col-lg-3 col-md-3 col-sm-12" style="width:100px"> 
						<div class="img-thumbnail" style="margin:0 auto; height: 250px; width:250px"><?php
							echo '<img class="img-fluid" src = "images_web/'.$table_photo["$ID_i[$i]"].'" height = 100% width = 100%>';?>       
						</div>
						<?php echo '<td>';
							echo '<a style="margin-left:2em" href = "'.$_SERVER['PHP_SELF'].'?idLien='.$var.'">'.$table_item["$ID_i[$i]"][1].'</a> </td>';  ?>
    				</div> <?php
			}

			if($c == 3 || $i == count($ID_i))
			{?>
    				<div class="col-lg-3 col-md-3 col-sm-12"> 
						<div class="img-thumbnail" style="margin:0 auto; height: 250px; width:250px"><?php
							echo '<img class="img-fluid" data-toggle="modal" data-target="#mod" src = "images_web/'.$table_photo["$ID_i[$i]"].'" height = 100% width = 100%>';?>       
						</div>
						<?php echo '<td>';
							echo '<a style="margin-left:2em" href = "'.$_SERVER['PHP_SELF'].'?idLien='.$var.'">'.$table_item["$ID_i[$i]"][1].'</a> </td>';  ?>
    				</div> 
    				<div class="col-lg-2 col-md-2 col-sm-12"></div> 
    			</div> <br><?php
    			$c = -1; 
			}
			$c++;
		}?>    
		<br><br><br><br><br><br><br><br><br><br><br><br>
		<footer class="page-footer container-fluid">   
			<div class="container">    
				<div class="row">       
					
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