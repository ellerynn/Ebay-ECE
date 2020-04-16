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
					<div class="list-group" style="position: fixed; width: 15%;">
						<h3 class="text-center">eBay ECE</h3>
						<p></p>
			        	<a href="#" class="list-group-item">Ferraille ou Trésor</a>
			          	<a href="#" class="list-group-item">Bon pour le Musée</a>
			          	<a href="#" class="list-group-item">Accessoires VIP</a>
			        </div>
		        </div>
		        <div class="col-lg-9 col-md-9 col-sm-12">
		        	<div style="margin-right: 5%;" id="carousel" class="carousel slide" data-ride="carousel">
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

		<!--Mettre en place une section à quatre colonnes-->
		<!--20 IMAGES-->
		<div class="container features">    
			<div class="row"> <!--A chaque fois qu'on crée des colonnes pour qu’elles servent de conteneur à la grille.-->  
				<div class="col-lg-3 col-md-3 col-sm-12"></div>
				<div class="col-lg-3 col-md-3 col-sm-12"> <!--colonnes et de la taille qu'elles auront sur différents écrans + signifie qu'elles occuperont un tiers de l'écran sur les grands et moyens écrans. (12 divisé par 4 est 3) mais l'écran entier sur de petits appareils (12 colonnes sur 12).-->
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid"> <!--Cela permet de les rendre réactifs afin qu'ils évoluent avec l'écran sur lequel la page est affichée.--> 
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div>               
				<div class="col-lg-3 col-md-3 col-sm-12">      	        
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid"> 
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div> 
				<div class="col-lg-3 col-md-3 col-sm-12">      	        
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid"> 
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div>  
			</div> 
			<br>
			<div class="row"> 
				<div class="col-lg-3 col-md-3 col-sm-12"></div>
				<div class="col-lg-3 col-md-3 col-sm-12">  
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid">  
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div>            
				<div class="col-lg-3 col-md-3 col-sm-12">      	        
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid"> 
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div> 
				<div class="col-lg-3 col-md-3 col-sm-12">      	        
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid"> 
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div>  
			</div> 
			<br>
			<div class="row"> 
				<div class="col-lg-3 col-md-3 col-sm-12"></div>
				<div class="col-lg-3 col-md-3 col-sm-12">  
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid">  
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div>            
				<div class="col-lg-3 col-md-3 col-sm-12">      	        
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid"> 
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div> 
				<div class="col-lg-3 col-md-3 col-sm-12">      	        
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid"> 
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div>  
			</div>
			<br>
			<div class="row"> 
				<div class="col-lg-3 col-md-3 col-sm-12"></div>
				<div class="col-lg-3 col-md-3 col-sm-12">  
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid">  
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div>            
				<div class="col-lg-3 col-md-3 col-sm-12">      	        
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid"> 
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div> 
				<div class="col-lg-3 col-md-3 col-sm-12">      	        
					<div class="img-thumbnail">
						<a href="attente_image.jpg">       
							<img src="attente_image.jpg" class="img-fluid"> 
						</a>     
						<p>En attente d'images d'items à vendre</p> 
					</div>
				</div>  
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
				</script> <?php
				exit();
			}?>
	</body> 
</html> 