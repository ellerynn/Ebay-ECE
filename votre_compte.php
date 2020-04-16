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
?>

<!DOCTYPE html> 
<html> 
	<head>  
		<title>Votre compte</title>  
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
			<div class="panel">
				<div class="panel-heading">
				   	<br><h2 class="text-center">Mes informations</h2><br>
				</div>
				<div class="panel-body">
						
		        </div>
		    </div>
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
						<h5 class="text-uppercase font-weight-bold"><a href="achat.php" id="achat">Achat</a></h5>
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
								<h5 class="text-uppercase font-weight-bold"><a href="vendre.php" id="vendre">Vendre</a></h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"><a href="votre_compte.php">Votre compte</a></h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"><a href="admin.php" id="admin">Admin</a></h5>
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
			if($_SESSION['Statut'] == ADMIN)
			{?>
				<script>
					//Cacher achat
					document.getElementById("ades").onclick = function() {
						var cache = document.getElementById("l3");
						cache.style.display = "none";
					}

					//Bloquer les liens
					document.getElementById("panier").onclick = function() {return false;}
					document.getElementById("achat").onclick = function() {return false;}
					document.getElementById("enchere").onclick = function() {return false;}
					document.getElementById("achetez").onclick = function() {return false;}
					document.getElementById("offre").onclick = function() {return false;}

					var x = document.getElementById("ad");
					x.style.display = "block";
				</script> <?php
			}

			elseif($_SESSION['Statut'] == VENDEUR)
			{?>
				<script>
					//Bloquer les liens onClick
					document.getElementById("ades").onclick = function() {
						var cachebis = document.getElementById("l3");
						cachebis.style.display = "none";
					}

					document.getElementById("panier").onclick = function() {return false;}
					document.getElementById("achat").onclick = function() {return false;}
					document.getElementById("enchere").onclick = function() {return false;}
					document.getElementById("achetez").onclick = function() {return false;}
					document.getElementById("offre").onclick = function() {return false;}
					document.getElementById("admin").onclick = function() {return false;}

					var liste = document.getElementById("lvendeur");
					liste.style.display ="block";

					var bouton = document.getElementById("btn_v_info");
					bouton.style.display ="block";

					document.getElementById("btn_v_info").onclick = function() {
						var m = document.getElementById("form_v_info");
						m.style.display = "block";
					}

					var panel_supp = document.getElementById("panel_supp_vendeur");
					var panel_i = document.getElementById("panel_info");
					var panel_mes = document.getElementById("panel_mes_vendeur");
					var panel_offres = document.getElementById("panel_o_vendeur");

					document.getElementById("bv1").onclick = function() {
						panel_supp.style.display ="block";
						panel_i.style.display ="none";
						panel_mes.style.display ="none";
						panel_offres.style.display ="none";
					}

					document.getElementById("bv2").onclick = function() {
						panel_supp.style.display ="none";
						panel_i.style.display ="block";
						panel_mes.style.display ="none";
						panel_offres.style.display ="none";
					}

					document.getElementById("bv3").onclick = function() {
						panel_supp.style.display ="none";
						panel_i.style.display ="none";
						panel_mes.style.display ="block";
						panel_offres.style.display ="none";
					}

					document.getElementById("bv4").onclick = function() {
						panel_supp.style.display ="none";
						panel_i.style.display ="none";
						panel_mes.style.display ="none";
						panel_offres.style.display ="block";
					}
				</script> <?php
			}

			elseif($_SESSION['Statut'] == ACHETEUR)
			{?>
				<script>
					document.getElementById("ades").onclick = function() {
						var cachebis = document.getElementById("l2");
						cachebis.style.display = "none";
					}

					document.getElementById("admin").onclick = function() {return false;}
					document.getElementById("vendre").onclick = function() {return false;}

					var liste = document.getElementById("lacheteur");
					liste.style.display ="block";
					
					var panel_i = document.getElementById("panel_info");
					var panel_mes = document.getElementById("panel_mes_acheteur");
					
					document.getElementById("bac1").onclick = function() {
						panel_i.style.display ="block";
						panel_mes.style.display ="none";
					}

					document.getElementById("bac2").onclick = function() {
						panel_i.style.display ="none";
						panel_mes.style.display ="block";
					}
				</script> <?php
			}				
			exit();?>
	</body> 
</html> 