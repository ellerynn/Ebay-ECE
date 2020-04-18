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

	//identifier votre BDD
    $database = "ebay ece paris";
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);
	$erreur ="";

	//On sélectionne tous les items présents dans la table item
	$sql = "SELECT * FROM item";
	$r = mysqli_query($db_handle, $sql);

	$table_item = array();
	$table_photo = array();

	$ID_i = array();
	$nom = array();
	$prix = array();
	$categorie = array();
	$temp = array();
	$c = 0;

	if($db_found)
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
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" id="ades">Mon eBay</button>
					  	<div class="dropdown-menu" id="menu-deroulant">
						    <a class="nav-link dropdown-item" href="achat.php" id="l3">Achat</a>
						    <a class="nav-link dropdown-item" href="vendre.php" id="l2">Vendre</a>
					  	</div>
					</li> 
					<li class="nav-item">
						<a style="display: none; transform: translateY(7px); color: white;" href="admin.php" id="ad">Admin.</a>
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
						<a class="nav-link" href="panier.php" id="panier"><i class="fas fa-shopping-cart"></i></a>
					</li>    
				</ul>      
			</div> 
		</nav>

		<header class="page-header header container-fluid"> <!-- -fluid permet de s'assurer que le conteneur s'étend sur toute la largeur de l'écran. Il y a aussi juste un container, avec des largeurs fixes appliquées = espace des deux côtés de l'écran.-->
			<div class="row"> 
				<div class="col-lg-3 col-md-3 col-sm-12">
					<div class="list-group" style="width: 80%;">
						<button class="btn btn-lg" id="b0" style="background-color: white; border :0;"><h3 class="text-center">eBay ECE</h3></button>
						<p></p>
			        	<button class="btn btn-lg" id="b1"><a href="#panel_ferraille_tresor">Ferraille ou trésor</a></button>
			          	<button class="btn btn-lg" id="b2"><a href="#panel_bon_musee">Bon pour le Musée</a></button>
			          	<button class="btn btn-lg" id="b3"><a href="#panel_vip">Accessoires VIP</a></button>
			        </div>
		        </div>
		        <div class="col-lg-9 col-md-9 col-sm-12">
		        	<div id="carousel" class="carousel slide" data-ride="carousel">
					 	<ul class="carousel-indicators">
					    	<li data-target="#carousel" data-slide-to="0" class="active"></li>
					    	<li data-target="#carousel" data-slide-to="1"></li>
					    	<li data-target="#carousel" data-slide-to="2"></li>
						</ul>

						<div class="carousel-inner">
						    <div class="carousel-item active">
						      	<img src="noir.png" alt="noir">
						    </div>
						    <div class="carousel-item">
						      	<img src="rose.png" alt="rose">
						    </div>
						    <div class="carousel-item">
						      	<img src="gris.png" alt="gris">
						    </div>
						</div>

						<a class="carousel-control-prev" href="#carousel" data-slide="prev">
						    <span class="carousel-control-prev-icon"></span>
						</a>
						<a class="carousel-control-next" href="#carousel" data-slide="next">
						    <span class="carousel-control-next-icon"></span>
						</a>
					</div>
		        </div>
	    	</div>
		</header>

		<!--DEBUT Ajout de code 2-->
	    <div class="container-fluid features">
	    	<div class="panel" style="display: none;" id="panel_ferraille_tresor">
			    <div class="panel-body">
			    	<br><h3 class="text-center">Ferraille ou Trésor</h3><br>
			    	<?php
			    		echo '<table class = "table">';
			    			echo '<tr>';
			    				echo '<td>Photo</td>';
			    				echo '<td>Nom</td>';
			    				echo '<td>Achat immédiat</td>';
			    				echo '<td>Meilleur offre</td>';
			    				echo '<td>Enchère</td>';
			    				//echo "<td>Prix (€)</td>";
			    			echo '</tr>';
			    		for ($i= 0; $i < count($table_item); $i++)
			    		{ //pour chaque item
			    			if ($table_item["$ID_i[$i]"][3] == "Farraille_tresor")
			    			{
			    				echo '<tr>';
			    				
									echo '<td><img src = "images_web/'.$table_photo["$ID_i[$i]"].'" height=100 width =100 ></td>';
								//}
									echo '<td>
											<a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$ID_i[$i].'">'.$table_item["$ID_i[$i]"][1].'</a>
										 </td>'; //Nom de l'item
									if ( (strlen($table_item["$ID_i[$i]"][3]) == 15 ) || (strlen($table_item["$ID_i[$i]"][3]) == 22 ) || (strlen($table_item["$ID_i[$i]"][3]) == 20 ) )
										echo '<td>oui</td>'; //Achat immédiat
									else
										echo '<td>non</td>';
									if ( (strlen($table_item["$ID_i[$i]"][3]) == 6 ) || (strlen($table_item["$ID_i[$i]"][3]) == 20 ) )
										echo '<td>oui</td>'; //Meilleur offre
									else
										echo '<td>non</td>'; 
									if ( (strlen($table_item["$ID_i[$i]"][3]) == 8 ) || (strlen($table_item["$ID_i[$i]"][3]) == 22 ) )
										echo '<td>oui</td>'; //Enchere
									else
										echo '<td>non</td>'; 
								echo '<tr>';
			    			}

			    		}
			    		echo '</table>';
			    	?>
		    	</div>
		    </div>
		    <div class="panel" style="display: none;" id="panel_bon_musee">
			    <div class="panel-body">
			    	<br><h3 class="text-center">Bon pour le Musée</h3><br>
			    	<?php
			    		echo '<table class = "table">';
			    			echo '<tr>';
			    				echo "<td>Photo</td>";
			    				echo "<td>Nom</td>";
			    				echo "<td>Achat immédiat</td>";
			    				echo "<td>Meilleur offre</td>";
			    				echo "<td>Enchère</td>";
			    				//echo "<td>Prix (€)</td>";
			    			echo "</tr>";

			    		for ($i= 0; $i < count($table_item); $i++){ //pour chaque item
			    			if ($table_item["$ID_i[$i]"][3] == "Musee"){
			    				echo "<tr>";
									echo '<td><img src = "images_web/'.$table_photo["$ID_i[$i]"].'" height=100 width =100 ></td>';
								//}
									echo '<td>
											<a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$ID_i[$i].'">'.$table_item["$ID_i[$i]"][1].'</a>
										 </td>'; //Nom de l'item
									if ( (strlen($table_item["$ID_i[$i]"][3]) == 15 ) || (strlen($table_item["$ID_i[$i]"][3]) == 22 ) || (strlen($table_item["$ID_i[$i]"][3]) == 20 ) )
										echo "<td>oui</td>"; //Achat immédiat
									else
										echo "<td>non</td>";
									if ( (strlen($table_item["$ID_i[$i]"][3]) == 6 ) || (strlen($table_item["$ID_i[$i]"][3]) == 20 ) )
										echo "<td>oui</td>"; //Meilleur offre
									else
										echo "<td>non</td>"; 
									if ( (strlen($table_item["$ID_i[$i]"][3]) == 8 ) || (strlen($table_item["$ID_i[$i]"][3]) == 22 ) )
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
		    <div class="panel" style="display: none;" id="panel_vip">
			    <div class="panel-body">
			    	<br><h3 class="text-center">Accessoires VIP</h3><br>
			    	<?php
			    		echo '<table class = "table">';
			    			echo '<tr>';
			    				echo "<td>Photo</td>";
			    				echo "<td>Nom</td>";
			    				echo "<td>Achat immédiat</td>";
			    				echo "<td>Meilleur offre</td>";
			    				echo "<td>Enchère</td>";
			    				//echo "<td>Prix (€)</td>";
			    			echo "</tr>";

			    		for ($i= 0; $i < count($table_item); $i++){ //pour chaque item
			    			if ($table_item["$ID_i[$i]"][3] == "VIP"){
			    				echo "<tr>";
			    				//on affiche tout les données des items de catégorie ferraille 
									echo '<td><img src = "images_web/'.$table_photo["$ID_i[$i]"].'" height=100 width =100 ></td>';
								//}
									echo '<td>
											<a href = "'.$_SERVER['PHP_SELF'].'?idLien='.$ID_i[$i].'">'.$table_item["$ID_i[$i]"][1].'</a>
										 </td>'; //Nom de l'item

									if ( (strlen($table_item["$ID_i[$i]"][3]) == 15 ) || (strlen($table_item["$ID_i[$i]"][3]) == 22 ) || (strlen($table_item["$ID_i[$i]"][3]) == 20 ) )
										echo "<td>oui</td>"; //Achat immédiat
									else
										echo "<td>non</td>";
									if ( (strlen($table_item["$ID_i[$i]"][3]) == 6 ) || (strlen($table_item["$ID_i[$i]"][3]) == 20 ) )
										echo "<td>oui</td>"; //Meilleur offre
									else
										echo "<td>non</td>"; 
									if ( (strlen($table_item["$ID_i[$i]"][3]) == 8 ) || (strlen($table_item["$ID_i[$i]"][3]) == 22 ) )
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
	   
		<?php
		//Pour chaque item
		for ($i = 0 ; $i<count($ID_i); $i++)
		{
			if($c == 0)
			{?>
				<div class="row">
					<div class="col-lg-3 col-md-3 col-sm-12"> 
						<div class="img-thumbnail" style="margin:0 auto; height: 250px;width:250px">
							<a href="attente_image.jpg"> <?php
								echo '<img class="img-fluid" src = "images_web/'.$table_photo["$ID_i[$i]"].'" height = 100% width = 100%>';?>       
							</a>     
						</div>
						<?php echo '<p style="margin-left:2em;">'.$table_item["$ID_i[$i]"][1].'<br>'.$table_item["$ID_i[$i]"][2].'€</p>';?>
    				</div> <?php
			}

			if($c == 1 || $c == 2)
			{?>
    				<div class="col-lg-3 col-md-3 col-sm-12" style="width:100px"> 
						<div class="img-thumbnail" style="margin:0 auto; height: 250px; width:250px">
							<a href="attente_image.jpg"> <?php
								echo '<img class="img-fluid" src = "images_web/'.$table_photo["$ID_i[$i]"].'" height = 100% width = 100%>';?>       
							</a>     
						</div>
						<?php echo '<p style="margin-left:2em;">'.$table_item["$ID_i[$i]"][1].'<br>'.$table_item["$ID_i[$i]"][2].'€</p>';?>
    				</div> <?php
			}

			if($c == 3 || $i == count($ID_i))
			{?>
    				<div class="col-lg-3 col-md-3 col-sm-12"> 
						<div class="img-thumbnail" style="margin:0 auto; height: 250px; width:250px">
							<a href="attente_image.jpg"> <?php
								echo '<img class="img-fluid" src = "images_web/'.$table_photo["$ID_i[$i]"].'" height = 100% width = 100%>';?>       
							</a>     
						</div>
						<?php echo '<p style="margin-left:2em;">'.$table_item["$ID_i[$i]"][1].'<br>'.$table_item["$ID_i[$i]"][2].'€</p>';?>
    				</div> 
    				<div class="col-lg-2 col-md-2 col-sm-12"></div> 
    			</div> <br><?php
    			$c = -1; 
			}
			$c++;
		}?>    

		<!--Créer un pied de page (footer)-->
		<footer class="page-footer container-fluid" style="border-color: ">   
			<div class="container">    
				<div class="row">       
					<div class="col-lg-3 col-md-3 col-sm-12">	
						<h5 class="text-uppercase font-weight-bold">Catégories</h5>
						<ul>  
							<li>
								<a href="#panel_ferraille_tresor" id="b4">Ferraille ou Trésor</a>
							</li>    
							<li>
								<a href="#panel_bon_musee" id="b5">Bon pour le Musée</a>
							</li> 
							<li>
								<a href="#panel_vip" id="b6">Accessoires VIP</a>
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
								<h5 class="text-uppercase font-weight-bold"> <a href="vendre.php" id="vendre">Vendre</a> </h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"> <a href="votre_compte.php">Votre compte</a> </h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"> <a href="admin.php" id="admin">Admin</a> </h5>
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
			if(empty($_SESSION['login'])) 
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
			{
				if($_SESSION['Statut'] == ADMIN)
				{?>
					<script>
						//Bloquer les liens onClick
						document.getElementById("ades").onclick = function() {
							var cache = document.getElementById("l3");
							cache.style.display = "none";
						}	

						var x = document.getElementById("ad");
						x.style.display = "block";

						document.getElementById("panier").onclick = function() {return false;}
						document.getElementById("achat").onclick = function() {return false;}
						document.getElementById("enchere").onclick = function() {return false;}
						document.getElementById("achetez").onclick = function() {return false;}
						document.getElementById("offre").onclick = function() {return false;}
					</script> <?php
				}

				elseif($_SESSION['Statut'] == VENDEUR)
				{?>
					<script>
						//Bloquer les liens onClick
						document.getElementById("ades").onclick = function() {
							var cache = document.getElementById("l3");
							cache.style.display = "none";
						}

						document.getElementById("panier").onclick = function() {return false;}
						document.getElementById("achat").onclick = function() {return false;}
						document.getElementById("enchere").onclick = function() {return false;}
						document.getElementById("achetez").onclick = function() {return false;}
						document.getElementById("offre").onclick = function() {return false;}
						document.getElementById("admin").onclick = function() {return false;}
					</script> <?php
				}

				elseif($_SESSION['Statut'] == ACHETEUR)
				{?>
					<script>
						document.getElementById("ades").onclick = function() {
							var cache = document.getElementById("l2");
							cache.style.display = "none";
						}
						
						document.getElementById("admin").onclick = function() {return false;}
						document.getElementById("vendre").onclick = function() {return false;}
					</script> <?php
				}?>
				
				<script>
					// Get the button, and when the user clicks on it, execute myFunction
					document.getElementById("btnpop").onclick = function() {
						document.getElementById("apop1").classList.toggle("show");
					}

					var panel_ferraille = document.getElementById("panel_ferraille_tresor");
					var panel_bon = document.getElementById("panel_bon_musee");
					var panel_vip = document.getElementById("panel_vip");

					document.getElementById("b0").onclick = function() {m0()}

					function m0()
					{
						panel_ferraille.style.display = "none";
						panel_bon.style.display = "none";
						panel_vip.style.display = "none";
					}

					document.getElementById("b1").onclick = function() {m1()}
					document.getElementById("b4").onclick = function() {m1()}

					function m1()
					{
						panel_ferraille.style.display = "block";
						panel_bon.style.display = "none";
						panel_vip.style.display = "none";
					}

					document.getElementById("b2").onclick = function() {m2()}
					document.getElementById("b5").onclick = function() {m2()}

					function m2()
					{
						panel_ferraille.style.display = "none";
						panel_bon.style.display = "block";
						panel_vip.style.display = "none";
					}

					document.getElementById("b3").onclick = function() {m3()}
					document.getElementById("b6").onclick = function() {m3()}

					function m3()
					{
						panel_ferraille.style.display = "none";
						panel_bon.style.display = "none";
						panel_vip.style.display = "block";
					}
				</script> <?php
				exit();
			}?>
	</body> 
</html> 