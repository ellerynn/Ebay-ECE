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
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" id="ades">Mon eBay</button>
					  	<div class="dropdown-menu" id="menu-deroulant">
						    <a class="nav-link dropdown-item" href="vendre.php" id="l2">Vendre</a>
						    <a class="nav-link dropdown-item" href="admin.php" id="l1">Admin</a>
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
						<i class="nav-link fas fa-shopping-cart" style="margin-top: 5px;"></i>
					</li>    
				</ul>      
			</div> 
		</nav>

		<br><br><br><br>
		<div class="container border rounded" id="vpdt">
            <div class="panel">
			    <div class="panel-heading">
			    	<br><h2 class="text-center">Vendre un produit</h2><br>
			    </div>
			    <div class="panel-body">
					<form method='post' action='ajout_produit.php' enctype='multipart/form-data'>
	                  	<div class="form-group">
	                    	<div class="row">
	                    		<div class="col-lg-6 col-md-6 col-sm-12">
	                    			<p class="font-weight-bold">Nom du produit</p>
	                        		<input class="form-control" style="width: 100%" type="text" name="nom" placeholder="Nom du produit" required>
	                        	</div>
		                    	<div class="col-lg-6 col-md-6 col-sm-12">
		                    		<p class="font-weight-bold">Prix</p>
		                        	<input class="form-control" type="number" style="width: 100%" name="prix" placeholder="Prix" required>
		                        </div>
		                    </div>
	                    </div>
	                    <div class="form-group">
	                    	<div class="row">
	                    		<div class="col-lg-6 col-md-6 col-sm-12">
	                    			<p class="font-weight-bold">Photo(s)</p>
	                      			<input type="file" name="filephoto[]" id="file" multiple>
	                        	</div>

		                    	<div class="col-lg-6 col-md-6 col-sm-12">
		                    		<p class="font-weight-bold">Vidéo (facultative)</p>
	                        		<input type="file" name="filevideo[]" id="file" multiple>
		                        </div>
		                    </div>
	                    </div>
	                    <div class="form-group">
	                    	<textarea name="description" rows="5" cols="100" placeholder="Description" id="description" required></textarea>
	                    </div>
	                    <div class="form-group">
	                    	<p class="font-weight-bold">Catégorie(s)</p>
	                        <input type="radio" name="categorie" value="Farraille_tresor" id="cb">Ferraille ou Trésor
							<input type="radio" name="categorie" value="Musee" id="cb">Bon pour le Musée
							<input type="radio" name="categorie" value="VIP" id="cb">Accessoire VIP
	                    </div>
	                    <!--A BLINDER-->
	                    <div class="form-group">
	                    	<p class="font-weight-bold">Type de vente</p>
	                    	<input type="checkbox" name="vente1" value="achat_immediat" id="cb">Achat immédiat 
							<input type="checkbox" name="vente2" value="enchere" style="margin-right: 5px;margin-left: 10px;" id="ench" onclick="montrer()">Enchère
							<input type="checkbox" name="vente2" value="offre" id="cb">Meilleur offre
						</div>
						<div class="form-group" style="display: none;" id="jpp">
							<div class="row">       
								<div class="col-lg-6 col-md-6 col-sm-12">
									Date de début : <input class="form-control" type="Date" name="datedebut"> 
								</div>
								<div class="col-lg-6 col-md-6 col-sm-12">
									Date de fin : <input class="form-control"  type="Date" name="datefin"> 
								</div>
							</div>
							<div class="row">       
								<div class="col-lg-4 col-md-4 col-sm-12">
									Heure de début : <input class="form-control" type="time" name="heuredebut">
								</div>
								<div class="col-lg-4 col-md-4 col-sm-12">
									Heure de fin : <input class="form-control" type="time" name="heurefin">
								</div>
								<div class="col-lg-4 col-md-4 col-sm-12">
									Prix de départ : <input class="form-control" type="number" name="prixdepart">
								</div>
							</div>
						</div>
						<div class="form-group">
	                    	<input name="boutonajoutproduit" type="submit" value="Ajouter le produit">
						</div>

						<script>
							function montrer() 
							{
								// Get the checkbox
								var checkBox = document.getElementById("ench");
								// Get the output text
								var text = document.getElementById("jpp");

								// If the checkbox is checked, display the output text
								if (checkBox.checked == true)
									text.style.display = "block";
								else 
									text.style.display = "none";
							}
						</script> 
               		</form>
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
						<h5 class="text-uppercase font-weight-bold">Achat</h5>
						<ul>  
							<li>
								Enchères
							</li>    
							<li>
								Achetez-le maintenant
							</li> 
							<li>
								Meilleure offre
							</li>               
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
			if($_SESSION['Statut'] == VENDEUR)
			{?>
				<script>
					//Bloquer les liens onClick
					document.getElementById("ades").onclick = function() {
						var cache = document.getElementById("l1");
						cache.style.display = "none";
					}		
					document.getElementById("admin").onclick = function() {return false;}
				</script> <?php
			}?>
	</body> 
</html> 