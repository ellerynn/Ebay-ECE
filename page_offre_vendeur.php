<?php
	

		if (mysqli_num_rows($result) == 0) {
				//Livre inexistant
				echo "Erreur, cet item n'est pas disponible. <br>";
			} 
		else {
			$i = 0;

			while ($data = mysqli_fetch_assoc($result) ) 
			{
				$ID_acheteur[$i] = $data['ID_acheteur'];
				$ID_vendeur[$i] = $data['ID_vendeur'];
				$ID_item[$i] = $data['ID_item'];
				$Prix_acheteur[$i] = $data['Prix_acheteur'];
				$Prix_vendeur[$i] = $data['Prix_vendeur'];
				$Tentative[$i] = $data['Tentative'];
				$Statut[$i] = $data['Statut'];
				$i++;
			}

			for ($u = 0 ; $u < count($ID_item) ; $u++){ //nombre d'item (duplicata comprise)
				$temp = array();
				$sql = "SELECT * FROM item WHERE ID_item LIKE '$ID_item[$u]';"; //retrouver les ID_items issu de ID_item[]
				$result = mysqli_query($db_handle,$sql);

				if (mysqli_num_rows($result) != 0){
					while ($data = mysqli_fetch_assoc($result) ) 
					{
						$i_temp = 0;
						$temp[$i_temp] = $ID_item[$u]; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
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

						$table_item["$ID_item[$u]"] = $temp; // Tableau associatif
					}
				}

				//toujours dans la boucle for: 
				$temp_photo = array();
				$sql = "SELECT * FROM photo WHERE ID_item LIKE '$ID_item[$u]'";
				$result = mysqli_query($db_handle, $sql);
				if (mysqli_num_rows($result) != 0){
					$v = 0;
					while ($data = mysqli_fetch_assoc($result) )  //extraction de toute les photos d'un item donnée ($u)
					{
						$temp_photo[$v] = $data['Nom_photo'];
						$v++;
					}
					$table_photo["$ID_item[$u]"]= $temp_photo; //array de photo dans tableau associatif
				}
			}

		}
		for ($a = 0 ; $a < count($ID_item) ; $a++){
			$buttonrefuser[$a] = "buttonrefuser_".$a;
			$buttonaccepter[$a] = "buttonaccepter_".$a;
			$buttonsoumettre[$a] = "buttonsoumettre_".$a;
		}

		for ($a = 0 ; $a < count($ID_item) ; $a++){
			if(isset($_POST["$buttonrefuser[$a]"]))
			{
				$sql = "UPDATE meilleur_offre SET Statut = '1' WHERE ID_item = $ID_item[$a] AND ID_vendeur = $ID_temporaire_vendeur AND ID_acheteur = $ID_acheteur[$a];";
				$result3 = mysqli_query($db_handle, $sql);
				header('Location: http://localhost/Ebay-ECE/page_offre_vendeur.php');
			}
		}
		for ($a = 0 ; $a < count($ID_item) ; $a++){
			if(isset($_POST["$buttonaccepter[$a]"]))
			{
				$sql = "UPDATE meilleur_offre SET Statut = '3' WHERE ID_item = $ID_item[$a] AND ID_vendeur = $ID_temporaire_vendeur AND ID_acheteur = $ID_acheteur[$a];";
				$result = mysqli_query($db_handle, $sql);
				$sql1 = "UPDATE meilleur_offre SET Statut = '4' WHERE ID_item = $ID_item[$a] AND ID_vendeur = $ID_temporaire_vendeur AND ID_acheteur != $ID_acheteur[$a]";
				$result1 = mysqli_query($db_handle, $sql1);
				header('Location: http://localhost/Ebay-ECE/page_offre_vendeur.php');
			}
		}
		for ($a = 0 ; $a < count($ID_item) ; $a++){
			if(isset($_POST["$buttonsoumettre[$a]"]))
			{
				$sql = "UPDATE meilleur_offre SET Statut = '1' , Prix_vendeur = $contre_offre WHERE ID_item = $ID_item[$a] AND ID_vendeur = $ID_temporaire_vendeur AND ID_acheteur = $ID_acheteur[$a];";
				$result = mysqli_query($db_handle, $sql);
				header('Location: http://localhost/Ebay-ECE/page_offre_vendeur.php');
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
						    <a class="nav-link dropdown-item" href="admin.php" id="l1">Admin</a>
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
            <h1 class="text-center"> Offre pour vos produits</h1>
            <?php
            $m=0;
            	for ($i = 0 ; $i<count($ID_acheteur); $i++){ //La taille du tableau ID_acheteur est pareil que le tableau ID_item 
            		if($Statut[$i] == 2){
            		//Affichage des images pour un item donnée :
					for ($u = 0 ; $u < count($table_photo["$ID_item[$i]"]); $u++){
						echo '<img src = "images_web/'.$table_photo["$ID_item[$i]"][$u].'" height=100 width =100 ><br>';
					}
	            	echo "<br>L'ID de acheteur: ".$ID_acheteur[$i]."<br>";
					echo "Le nom de l'item: ".$table_item["$ID_item[$i]"][1]."<br>";
					
					echo "Le ID de item: ".$ID_item[$i]."<br>";
					echo "Offre que vous propose acheteur: ".$Prix_acheteur[$i]."<br>";
					echo "Votre prix: ".$Prix_vendeur[$i]."<br>";
					echo "C'est ca: ".$Tentative[$i]." tentative. <br>";
					echo "Le statut de offre: ".$Statut[$i]."<br>";

					echo '<form action="" method="post">';
					echo '<input class="btn border btn-outline-secondary rounded-lg" name="'.$buttonrefuser[$i].'" type="submit" value="Refuser la proposition"><br>';
					echo '<input class="btn border btn-outline-secondary rounded-lg" name="'.$buttonaccepter[$i].'" type="submit" value="Accepter la proposition"><br>';
					echo '<td><input type="text" name="contre_offre" placeholder="Contre Offre"></td><br>';
					echo '<input class="btn border btn-outline-secondary rounded-lg" name="'.$buttonsoumettre[$i].'" type="submit" value="Soumettre la contre Offre"><br>';
					echo '</form>';

					$m++;
				}
				}
				
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