<?php
	include("const.php");
	// On prolonge la session
	session_start();

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
	  header('Location: connexion.php');
	  exit();
	}

	$database = "ebay ece paris";
	$db_handle = mysqli_connect('localhost', 'root', '');
	$db_found = mysqli_select_db($db_handle, $database);
	//Déclaration de variable
	$table_item = array();
	$table_photo = array();
	$table_vendeur = array();
	$table_enchere = array();
	
	if ($db_found)
	{
		//Récupération des données de la table item
		$sql = "SELECT * FROM item"; // récupération de tout les items
		$result = mysqli_query($db_handle,$sql);
		if (mysqli_num_rows($result) != 0){
			$temp = array();
			$i = 0;
			while ($data = mysqli_fetch_assoc($result) ) 
			{
				$i_temp = 0;
				$temp[$i_temp] = $data['ID_item']; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
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

				$table_item["$i"] = $temp; //$i comme clée, car sinon on peut plus retrouver l'ID_item
				$i++;
			}
		}
		//Récupération de la table photo pour chaque items
		for ($i = 0 ; $i < count($table_item); $i++){
			$sql = "SELECT * FROM photo WHERE ID_item = ".$table_item[$i][0];  //récupération des photos d'un item donnée
			$result = mysqli_query($db_handle,$sql);
			if (mysqli_num_rows($result) != 0){
				$v = 0;
				$temp = array();	
				while ($data = mysqli_fetch_assoc($result) ) //extraction de toute les photos d'un item donnée
				{
					$temp[$v] = $data['Nom_photo'];
					$v++;
				}
				$var = $table_item[$i][0]; // Il semblerait que php ne veut pas $table_photo[$table_item[$i][0]]
				$table_photo["$var"]= $temp;   //clée ID_item car on sait qu'il est stocké dans $table_item[$i][0]
			}
		}
		//Récupération de la table vendeur (le pseudo du vendeur de l'item)
		for ($i = 0 ; $i < count($table_item); $i++){
			$sql = "SELECT Pseudo FROM vendeur WHERE ID = ".$table_item[$i][2]; //emplacement où se trouve l'id vendeur
			$result = mysqli_query($db_handle,$sql);
			if (mysqli_num_rows($result) != 0){
				while ($data = mysqli_fetch_assoc($result) ) //extraction de toute les photos d'un item donnée
				{
					$var = $table_item[$i][0];
					$table_vendeur["$var"] = "[Vendeur] ".$data['Pseudo'];
				}
			}else //Si le vendeur n'est pas trouvé, c'est un administrateur
			{
				$sqlContour = "SELECT Prenom FROM personne WHERE ID = ".$table_item[$i][2];
				$resultContour = mysqli_query($db_handle,$sqlContour);
				if (mysqli_num_rows($resultContour) != 0)
				{
					while ($data = mysqli_fetch_assoc($resultContour) ) //extraction de toute les photos d'un item donnée
					{
						$var = $table_item[$i][0];
						$table_vendeur["$var"] = "[Admin] ".$data['Prenom'];
					}
				}
			}
		}
		//Récupération de la table enchère
		for ($i = 0 ; $i < count($table_item); $i++){
			$sql = "SELECT * FROM liste_enchere WHERE ID_item = ".$table_item[$i][0]; //emplacement où se trouve l'id vendeur
			$result = mysqli_query($db_handle,$sql);
			if (mysqli_num_rows($result) != 0)
			{
				$temp = array();
				while ($data = mysqli_fetch_assoc($result) ) //extraction de toute les photos d'un item donnée
				{
					$i_temp = 0;
					$temp[$i_temp] = $data['Date_debut']; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
					$i_temp++;
					$temp[$i_temp] = $data['Heure_debut']; // i_temp = 1
					$i_temp++;
					$temp[$i_temp] = $data['Date_fin']; // i_temp = 2
					$i_temp++;
					$temp[$i_temp] = $data['Heure_fin']; // i_temp = 3
					$i_temp++;
					$temp[$i_temp] = $data['Prix_premier']; // i_temp = 4
				}
				$var = $table_item[$i][0];
				$table_enchere["$var"] = $temp;
			}
		}

		//Envoie de message
		$objet = isset($_POST["objet"])? $_POST["objet"] : "";
		$message = isset($_POST["message"])? $_POST["message"] : "";
		$erreur ="";
		if (isset($_POST["buttonenvoyer"])) 
		{
		  	if ($objet == "") 
	            $erreur .= "Objet est vide. <br>";
	        if ($message == "") 
	            $erreur .= "Message est vide. <br>";
	        if ($erreur == "") 
	        {
	        	if ($db_found) 
	            {	
	            	$sql = "INSERT INTO contact(ID_admin, ID_acheteur, Message, Objet) VALUES ('3','$id','$message','$objet');";
	                $result = mysqli_query($db_handle, $sql);
	            }
	        }
		}
	}//END
	else
		echo "Database not found";
	
	if (isset($_GET['idLien']))
	{ // Si un lien en particulier est cliqué : On récupère la valeur de idLien (dedans ctn l'id de l'item)
		$sql = "SELECT * from item WHERE ID_item = ".$_GET['idLien'].""; // On vérifie quand même s'il existe dans la BDD
		$result = mysqli_query($db_handle, $sql);	
		if (mysqli_num_rows($result) != 0)
		{ //Si l'objet existe, on le stock dans la session et on le renvoi à la page page_produit.php
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

			<form id="barre" action="rechercher.php" class="navbar-form inline-form">
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
						<a class="nav-link" href="panier.php" id="panier"><i class="fas fa-shopping-cart"></i></a>
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
			          	<button type="button" class="list-group-item btn" id="categories">Par catégories</button>
			          	<button type="button" class="list-group-item btn" id="ventes">Par type de vente</button>
						<button type="button" class="list-group-item btn" id="messages">Messages</button>			        	
			        </div>	
			    </div>
			    <div class="col-lg-9 col-md-9 col-sm-12" style="position: relative; min-height: 400px;">
				    <div class="panel" style="display: block;" id="panel_categorie">
					    <div class="panel">
						    <div class="panel-heading">
						    	<br><h2 class="text-center">Ferraille ou Trésor</h2><br>
						    </div>
						    <div class="panel-body">
						    	<?php
						    		echo '<table class = "table">';
						    			echo '<tr>';
						    				echo '<td>Photo</td>';
						    				echo '<td>Nom</td>';
						    				echo '<td>Vendeur</td>';
						    				echo '<td>Achat immédiat</td>';
						    				echo '<td>Meilleur offre</td>';
						    				echo '<td>Enchère</td>';
						    				//echo "<td>Prix (€)</td>";
						    			echo '</tr>';
						    		for ($i= 0; $i < count($table_item); $i++)
						    		{ //pour chaque item
						    			if ($table_item[$i][5] == "Ferraille_tresor")
						    			{
						    				echo '<tr>';
						    				//on affiche tout les données des items de catégorie ferraille 
						    				$var = $table_item[$i][0]; //on récuprère l'id de l'item puisque le tbx en bas, veux pas
						    				//for ($u = 0 ; $u < count($table_photo[$var]); $u++){ //Si on veut afficher plusieurs image de l'item
												echo '<td><img src = "images_web/'.$table_photo["$var"][0].'" height=100 width =100 ></td>';
											//}
												echo '<td>
														<a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$var.'">'.$table_item[$i][1].'</a>
													 </td>'; //Nom de l'item
												echo '<td>'.$table_vendeur["$var"].'</td>'; //Vendeur de l'item
												if ( (strlen($table_item[$i][3]) == 15 ) || (strlen($table_item[$i][3]) == 22 ) || (strlen($table_item[$i][3]) == 20 ) )
													echo '<td>oui</td>'; //Achat immédiat
												else
													echo '<td>non</td>';
												if ( (strlen($table_item[$i][3]) == 6 ) || (strlen($table_item[$i][3]) == 20 ) )
													echo '<td>oui</td>'; //Meilleur offre
												else
													echo '<td>non</td>'; 
												if ( (strlen($table_item[$i][3]) == 8 ) || (strlen($table_item[$i][3]) == 22 ) )
													echo '<td>oui</td>'; //Enchere
												else
													echo '<td>non</td>'; 
												//echo "<td>".$table_item[$i][6]."</td>"; //Prix
											echo '<tr>';
						    			}

						    		}
						    		echo '</table>';
						    	?>
					        </div>
					    </div>
					    <div class="panel">
						    <div class="panel-heading">
						    	<br><h2 class="text-center">Bon pour le musée</h2><br>
						    </div>
						    <div class="panel-body">
						    	<?php
						    		echo '<table class = "table">';
						    			echo '<tr>';
						    				echo "<td>Photo</td>";
						    				echo "<td>Nom</td>";
						    				echo "<td>Vendeur</td>";
						    				echo "<td>Achat immédiat</td>";
						    				echo "<td>Meilleur offre</td>";
						    				echo "<td>Enchère</td>";
						    				//echo "<td>Prix (€)</td>";
						    			echo "</tr>";

						    		for ($i= 0; $i < count($table_item); $i++){ //pour chaque item
						    			if ($table_item[$i][5] == "Musee"){
						    				echo "<tr>";
						    				//on affiche tout les données des items de catégorie ferraille 
						    				$var = $table_item[$i][0]; //on récuprère l'id de l'item puisque le tbx en bas, veux pas
						    				//for ($u = 0 ; $u < count($table_photo[$var]); $u++){ //Si on veut afficher plusieurs image de l'item
												echo '<td><img src = "images_web/'.$table_photo["$var"][0].'" height=100 width =100 ></td>';
											//}
												echo '<td>
														<a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$var.'">'.$table_item[$i][1].'</a>
													 </td>'; //Nom de l'item
												echo "<td>".$table_vendeur["$var"]."</td>"; //Vendeur de l'item
												if ( (strlen($table_item[$i][3]) == 15 ) || (strlen($table_item[$i][3]) == 22 ) || (strlen($table_item[$i][3]) == 20 ) )
													echo "<td>oui</td>"; //Achat immédiat
												else
													echo "<td>non</td>";
												if ( (strlen($table_item[$i][3]) == 6 ) || (strlen($table_item[$i][3]) == 20 ) )
													echo "<td>oui</td>"; //Meilleur offre
												else
													echo "<td>non</td>"; 
												if ( (strlen($table_item[$i][3]) == 8 ) || (strlen($table_item[$i][3]) == 22 ) )
													echo "<td>oui</td>"; //Enchere
												else
													echo "<td>non</td>"; 
												//echo "<td>".$table_item[$i][6]."</td>"; //Prix
											echo "<tr>";
						    			}
						    		}
						    		echo "</table>";
						    	?>
					        </div>
					    </div>
					    <div class="panel">
						    <div class="panel-heading">
						    	<br><h2 class="text-center">Accessoire VIP</h2><br>
						    </div>
						    <div class="panel-body">
						    	<?php
						    		echo '<table class = "table">';
						    			echo '<tr>';
						    				echo "<td>Photo</td>";
						    				echo "<td>Nom</td>";
						    				echo "<td>Vendeur</td>";
						    				echo "<td>Achat immédiat</td>";
						    				echo "<td>Meilleur offre</td>";
						    				echo "<td>Enchère</td>";
						    				//echo "<td>Prix (€)</td>";
						    			echo "</tr>";

						    		for ($i= 0; $i < count($table_item); $i++){ //pour chaque item
						    			if ($table_item[$i][5] == "VIP"){
						    				echo "<tr>";
						    				//on affiche tout les données des items de catégorie ferraille 
						    				$var = $table_item[$i][0]; //on récuprère l'id de l'item puisque le tbx en bas, veux pas
						    				//for ($u = 0 ; $u < count($table_photo[$var]); $u++){ //Si on veut afficher plusieurs image de l'item
												echo '<td><img src = "images_web/'.$table_photo["$var"][0].'" height=100 width =100 ></td>';
											//}

												echo '<td>
														<a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$var.'">'.$table_item[$i][1].'</a>
													 </td>'; //Nom de l'item

												echo "<td>".$table_vendeur["$var"]."</td>"; //Vendeur de l'item
												if ( (strlen($table_item[$i][3]) == 15 ) || (strlen($table_item[$i][3]) == 22 ) || (strlen($table_item[$i][3]) == 20 ) )
													echo "<td>oui</td>"; //Achat immédiat
												else
													echo "<td>non</td>";
												if ( (strlen($table_item[$i][3]) == 6 ) || (strlen($table_item[$i][3]) == 20 ) )
													echo "<td>oui</td>"; //Meilleur offre
												else
													echo "<td>non</td>"; 
												if ( (strlen($table_item[$i][3]) == 8 ) || (strlen($table_item[$i][3]) == 22 ) )
													echo "<td>oui</td>"; //Enchere
												else
													echo "<td>non</td>"; 
												//echo "<td>".$table_item[$i][6]."</td>"; //Prix
											echo "<tr>";
						    			}

						    		}
						    		echo "</table>";
						    	?>
					        </div>
					    </div>
					</div>

					<div class="panel" style="display: none;" id="panel_vente">
					    <div class="panel">
						    <div class="panel-heading">
						    	<br><h2 class="text-center">Achat immédiat</h2><br>
						    </div>
						    <div class="panel-body">
						    	<?php
						    		echo '<table class = "table">';
						    			echo '<tr>';
						    				echo "<td>Photo</td>";
						    				echo "<td>Nom</td>";
						    				echo "<td>Vendeur</td>";
						    				echo "<td>Prix (€)</td>";
						    			echo "</tr>";

						    		for ($i= 0; $i < count($table_item); $i++){ //pour chaque item
						    			if ((strlen($table_item[$i][3]) == 15 ) || (strlen($table_item[$i][3]) == 22 ) || (strlen($table_item[$i][3]) == 20 )){
						    				echo "<tr>";
						    				//on affiche tout les données des items de catégorie ferraille 
						    				$var = $table_item[$i][0]; //on récuprère l'id de l'item puisque le tbx en bas, veux pas
						    				//for ($u = 0 ; $u < count($table_photo[$var]); $u++){ //Si on veut afficher plusieurs image de l'item
												echo '<td><img src = "images_web/'.$table_photo["$var"][0].'" height=100 width =100 ></td>';
											//}
												echo '<td>
														<a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$var.'">'.$table_item[$i][1].'</a>
													 </td>'; //Nom de l'item
												echo "<td>".$table_vendeur["$var"]."</td>"; //Vendeur de l'item
												echo "<td>".$table_item[$i][6]."</td>"; //Prix
											echo "<tr>";
						    			}

						    		}
						    		echo "</table>";
						    	?>					
					        </div>
					    </div>
					    <div class="panel">
						    <div class="panel-heading">
						    	<br><h2 class="text-center">Meilleur offre</h2><br>
						    </div>
						    <div class="panel-body">
						    	<?php
						    		echo '<table class = "table">';
						    			echo '<tr>';
						    				echo "<td>Photo</td>";
						    				echo "<td>Nom</td>";
						    				echo "<td>Vendeur</td>";
						    				echo "<td>Prix (€)</td>";
						    			echo "</tr>";

						    		for ($i= 0; $i < count($table_item); $i++){ //pour chaque item
						    			if ((strlen($table_item[$i][3]) == 6 ) || (strlen($table_item[$i][3]) == 20 ) ){
						    				echo "<tr>";
						    				//on affiche tout les données des items de catégorie ferraille 
						    				$var = $table_item[$i][0]; //on récuprère l'id de l'item puisque le tbx en bas, veux pas
						    				//for ($u = 0 ; $u < count($table_photo[$var]); $u++){ //Si on veut afficher plusieurs image de l'item
												echo '<td><img src = "images_web/'.$table_photo["$var"][0].'" height=100 width =100 ></td>';
											//}
												echo '<td>
														<a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$var.'">'.$table_item[$i][1].'</a>
													 </td>'; //Nom de l'item
												echo "<td>".$table_vendeur["$var"]."</td>"; //Vendeur de l'item
												echo "<td>".$table_item[$i][6]."</td>"; //Prix
											echo "<tr>";
						    			}

						    		}
						    		echo "</table>";
						    	?>					
					        </div>
					    </div>
					    <div class="panel">
						    <div class="panel-heading">
						    	<br><h2 class="text-center">Enchère </h2><br>
						    </div>
						    <div class="panel-body">
						    	<?php
						    		echo '<table class = "table">';
						    			echo '<tr>';
						    				echo "<td>Photo</td>";
						    				echo "<td>Nom</td>";
						    				echo "<td>Vendeur</td>";
						    				echo "<td>Début</td>";
						    				echo "<td>Fin</td>";
						    				echo "<td>Prix actuelle (€)</td>";
						    			echo "</tr>";

						    		for ($i= 0; $i < count($table_item); $i++){ //pour chaque item
						    			if ((strlen($table_item[$i][3]) == 8 ) || (strlen($table_item[$i][3]) == 22 )){
						    				echo "<tr>";
						    				//on affiche tout les données des items de catégorie ferraille 
						    				$var = $table_item[$i][0]; //on récuprère l'id de l'item puisque le tbx en bas, veux pas
						    				//for ($u = 0 ; $u < count($table_photo[$var]); $u++){ //Si on veut afficher plusieurs image de l'item
												echo '<td><img src = "images_web/'.$table_photo["$var"][0].'" height=100 width =100 ></td>';
											//}
												echo '<td>
														<a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$var.'">'.$table_item[$i][1].'</a>
													 </td>'; //Nom de l'item
												echo "<td>".$table_vendeur["$var"]."</td>"; //Vendeur de l'item
												echo "<td>".$table_enchere["$var"][0]." à ".$table_enchere["$var"][1]."</td>";
												echo "<td>".$table_enchere["$var"][2]." à ".$table_enchere["$var"][3]."</td>";
												echo "<td>".$table_enchere["$var"][4]."</td>"; //Prix
											echo "<tr>";
						    			}

						    		}
						    		echo "</table>";
						    	?>					
					        </div>
					    </div>
					</div>
					<!-- SECTION MESSAGE POUR SAISIR SON MESSAGE-->
				    <div class="panel" style="display: none;" id="panel_messages">
					    <div class="panel-heading">
					    	<br><h2 class="text-center">Messages</h2><br>
					    </div>
					    <div class="panel-body">
					    	<form method="post" action="" enctype="multipart/form-data">
						       	<div class="form-group">
						          	<div class="row">
						          		<div class="col-lg-6 col-md-6 col-sm-12">
						           			<p class="font-weight-bold">Objet de votre message</p>
						               		<input class="form-control" style="width: 100%" type="text" name="objet" placeholder="Objet" required>
						               	</div>
							        </div>
						        </div>
						        <div class="form-group">
						            <textarea name="message" rows="5" cols="100" placeholder="Message" id="message" required></textarea>
						        </div>
						        <div class="form-group">
						           	<input class="form-control" style="width:200px; margin: 0 auto" name="buttonenvoyer" type="submit" value="Envoyer votre message">
								</div>
						    </form>					
				        </div>
					</div>
			    </div>
			</div>
		</div>
		
		<!--Créer un pied de page (footer)-->
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

		<script type="text/javascript">
			var mes = document.getElementById("panel_messages");
			var cat = document.getElementById("panel_categorie");
			var ven = document.getElementById("panel_vente");

			document.getElementById("categories").onclick = function() {
				cat.style.display = "block";
				mes.style.display = "none";
				ven.style.display = "none";
			}

			document.getElementById("ventes").onclick = function() {
				mes.style.display = "none";
				ven.style.display = "block";
				cat.style.display = "none";
			}

			document.getElementById("messages").onclick = function() {
				mes.style.display = "block";
				ven.style.display = "none";
				cat.style.display = "none";
			}
		</script>		
	</body> 
</html> 