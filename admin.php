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
	  // Si inexistante ou nulle, on redirige vers le formulaire de login
	  header('Location: connexion.php');
	  exit();
	}

	//identifier votre BDD
    $database = "ebay ece paris";
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);
	$erreur ="";

	$table_item = array();
	$table_photo = array();
	$nom_item = array();
	$ID_item = array();
	$ID_vendeur = array();
	$table_vendeur = array();

	//Récuperation des ID_item du vendeur (dont admins) connecté
	$sql1 = "SELECT * FROM item";
	$result1 = mysqli_query($db_handle, $sql1);

	if (mysqli_num_rows($result1) != 0) 
	{
		$i=0; $j=0;
		$temp = array();
		while ($data = mysqli_fetch_assoc($result1)) 
		{
			$ID_item[$i] = $data['ID_item'];
			$i++;
		
			$i_temp = 0;
			$temp[$i_temp] = $ID_item[$j]; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
			$i_temp++;
			$temp[$i_temp] = $data['Nom_item']; // i_temp = 1

			$table_item["$ID_item[$j]"] = $temp; // Tableau associatif
			$j++;
		}
	}	

	//Récuperation de la première photo de chaque item du vendeur
	for($a=0; $a < count($ID_item); $a++)
	{
		$sqlp = "SELECT * FROM photo WHERE ID_item LIKE '$ID_item[$a]' ";
		$req = mysqli_query($db_handle, $sqlp);
		
		if (mysqli_num_rows($req) != 0) 
		{
			$data = mysqli_fetch_assoc($req);
			$photo = $data['Nom_photo'];
			$table_photo["$ID_item[$a]"]= $photo; //array de photo dans tableau associatif
		}
	}

	//Récuperation des ID_item du vendeur (dont admins) connecté
	$sql2 = "SELECT * FROM vendeur";
	$result2 = mysqli_query($db_handle, $sql2);

	if (mysqli_num_rows($result2) != 0) 
	{
		$i=0; $j=0;
		$temp = array();
		while ($data = mysqli_fetch_assoc($result2)) 
		{
			$ID_vendeur[$i] = $data['ID'];
			$i++;
		
			$i_temp = 0;
			$temp[$i_temp] = $ID_vendeur[$j]; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
			$i_temp++;
			$temp[$i_temp] = $data['Pseudo']; // i_temp = 1

			$table_vendeur["$ID_vendeur[$j]"] = $temp; // Tableau associatif
			$j++;
		}
	}	
	//transfert de valeurs : récepteur 
	$erreurAjoutVendeur = "";
	if (isset($_GET['idErreur']))
	{ 
		$erreurAjoutVendeur = $_GET['idErreur'];
	}

?>

<!DOCTYPE html> 
<html> 
	<head>  
		<title>Admin</title>  
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
				<ul class="navbar-nav">    
					<li class="nav-item">
						<a class="nav-link" href="accueil.php">Accueil</a>
					</li>
					<li class="nav-item dropdown">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Mon eBay</button>
					  	<div class="dropdown-menu" id="menu-deroulant">
						    <a class="nav-link dropdown-item" href="vendre.php">Vendre</a>
					  	</div>
					</li>  
					<li class="nav-item">
						<a class="nav-link" href="admin.php">Admin.</a>
					</li>
					<li class="nav-item dropdown">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"><i class="fas fa-user"></i></button>
					  	<div class="dropdown-menu" id="menu-deroulant">
						    <a class="nav-link dropdown-item" href="votre_compte.php">Mon compte</a>
						    <a class="nav-link dropdown-item" href="deconnexion.php">Se déconnecter</a>
					  	</div>
					</li> 
					<li class="nav-item">
						<i class="nav-link fas fa-shopping-cart"></i>
					</li>    
				</ul>      
			</div> 
		</nav>

		<br><br><br>
		<div class="container features">
			<div class="row"> 
				<div class="col-lg-3 col-md-3 col-sm-12">
					<h3 class="text-center">eBay ECE</h3>
					<p></p>
					<div class="list-group">
			        	<button type="button" class="list-group-item btn" id="bad1">Supprimer un item du site</button>
			        	<button type="button" class="list-group-item btn" id="bad2">Gérer les vendeurs</button>
			        </div>
				</div>
		        <div class="col-lg-9 col-md-9 col-sm-12" style="position: relative; min-height: 400px;">
		        	<div class="panel" id="panel_supp_admin">
					    <div class="panel-body row">
					    	<div class="col-lg-6 col-md-6 col-sm-12" style="position: relative; min-height: 400px;">
					    		<br><h2 class="text-center">Supprimer un item du site</h2><br>
							    <form action="suppression_produit_admin.php" method="post">
									<div class="form-group">
				                        <input class="form-control" style="width: 50%; margin: 0 auto" type="number" name="id" placeholder="ID de l'article" required>
				                    </div>
				                    <div class="form-group">
				                    	<input class="form-control" style="width: 30%; margin: 0 auto" name="buttonsupprimer" type="submit" value="Supprimer">
									</div>
								</form>	
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12" style="position: relative; min-height: 400px;">
								<br><h2 class="text-center">Liste items</h2><br>
								<?php
									if(count($ID_item)==0)	
									{?>
										<div class="panel-body row border" style="width: 80%; margin: 0 auto; padding-top: 10px;"><?php 
											echo '<p class = "text-center">Aucun item.</p>';?>
										</div><?php
									}
									else
									{						
										for ($i = 0 ; $i<count($ID_item); $i++)
										{?>
											<div class="panel-body row border" style="width: 80%; margin: 0 auto; margin-bottom: 5px; padding: 2px;">
												<div class="col-lg-3 col-md-3 col-sm-12"><?php 
													echo '<img src = "images_web/'.$table_photo["$ID_item[$i]"].'" height = 50 width = 50 >';?>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-12"><?php 
													echo "ID de l'item : ".$table_item["$ID_item[$i]"][0]."<br>";
													echo "Nom de l'item : ".$table_item["$ID_item[$i]"][1];?>
												</div>
											</div> <?php
										}
									}
								?>
							</div>	
							<?php
							if($erreurAjoutVendeur != "")
					    	{
					    		echo $erreurAjoutVendeur;
					    	}	
					    	?>
				        </div>
				    </div>
				    <div class="panel" style="display: none;" id="panel_gv_admin">
					    <div class="panel-body border" style="border-radius: 5px;">
					    	<div class="row">
						    	<div class="col-lg-4 col-md-4 col-sm-12 border-right" style="padding: 1em;">
						    		<h4 class="text-center">Ajouter un vendeur</h4>
									<form action="ajout_vendeur.php" method="post" style="margin-left: 1em;">
										<div class="form-group">
					                        <input class="form-control" style="margin-bottom: 5px;" type="text" name="nom" placeholder="Nom" required>
					                        <input class="form-control" style="margin-bottom: 5px;" type="text" name="prenom" placeholder="Prénom" required>
					                        <input class="form-control" style="margin-bottom: 5px;" type="text" name="pseudo" placeholder="Pseudo" required>
					                        <input class="form-control" style="margin-bottom: 5px;" type="email" name="email" placeholder="Mail" required>
					                    	<input class="form-control" style="width: 50%; margin: 0 auto" name="buttonajoutervendeur" type="submit" value="Ajouter">
										</div>
									</form>
						    	</div>
						    	<div class="col-lg-4 col-md-4 col-sm-12" style="padding: 1em;">
						    		<h4 class="text-center">Supprimer un vendeur</h4>
									<form action="suppression_vendeur.php" method="post">
										<div class="form-group">
					                        <input class="form-control" style="margin: 0 auto; margin-bottom: 5px;" type="number" name="id" placeholder="ID vendeur" required>
					                        <input class="form-control" style="margin: 0 auto; margin-bottom: 5px;" type="text" name="pseudo" placeholder="Pseudo" required>
					                    	<input class="form-control" style="width: 50%; margin: 0 auto" name="buttonsupprimervendeur" type="submit" value="Supprimer">
										</div>
									</div>
						    	<div class="col-lg-4 col-md-4 col-sm-12" style="padding: 1em;">
						    		<h4 class="text-center">Liste vendeurs</h4>
						    		<?php
									if(count($ID_vendeur)==0)	
									{?>
										<div class="panel-body row border" style="width: 80%; margin: 0 auto; padding-top: 10px;"><?php 
											echo '<p class = "text-center">Aucun vendeur.</p>';?>
										</div><?php
									}
									else
									{						
										for ($i = 0 ; $i<count($ID_vendeur); $i++)
										{?>
											<div class="panel-body row border" style="width: 80%; margin: 0 auto; margin-bottom: 5px; padding: 2px;"><?php 
													echo "ID du vendeur : ".$table_vendeur["$ID_vendeur[$i]"][0]."<br>";
													echo "Pseudo du vendeur : ".$table_vendeur["$ID_vendeur[$i]"][1];?>
											</div> <?php
										}
									}
								?>
						    	</div>		
						    </div>	
				        </div>
				         <?php
					    	if($erreurAjoutVendeur != "")
					    	{
					    		echo $erreurAjoutVendeur;
					    	}
				    	?>	
				    </div>
		        </div>
		    </div>
		</div>
		    			
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
						<h5 class="text-uppercase font-weight-bold">Achat</h5>
						<ul>  
							<li>Enchères</li>    
							<li>Achetez-le maintenant</li> 
							<li>Meilleure offre</li>               
						</ul> 
					</div>   
					<div class="col-lg-3 col-md-3 col-sm-12">	
						<ul>  
							<li>
								<h5 class="text-uppercase font-weight-bold"> <a href="vendre.php">Vendre</a> </h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"> <a href="votre_compte.php">Votre compte</a> </h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"> <a href="admin.php">Admin</a> </h5>
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

		<script type="text/javascript">
			var panel_supp = document.getElementById("panel_supp_admin");
			var panel_gv = document.getElementById("panel_gv_admin");

			document.getElementById("bad1").onclick = function() {
				panel_supp.style.display ="block";
				panel_gv.style.display ="none";
			}

			document.getElementById("bad2").onclick = function() {
				panel_supp.style.display ="none";
				panel_gv.style.display ="block";
			}
		</script>
	</body> 
</html> 