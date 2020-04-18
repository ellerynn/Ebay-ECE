<?php
	include("const.php");

	//date_default_timezone_set('Europe/Paris');
	//$today = getdate();
	echo "<br><br><br><br>";
	//print_r($today);
	// On prolonge la session
	//session_start();
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
	  	//header('Location: connexion.php');
	  	//exit();
	}
	    
	//identifier votre BDD
    $database = "ebay ece paris";
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);

    $objet = "";
	$message = "";
	$ID_temporaire_acheteur = 29; //test sinon faut mettre 29, jai mis 2 pour desctiver la boucle decriture
	$ID_temporaire_admin = 3;

	if ($db_found) 
    {	
        	$sql1 = "SELECT * FROM contact WHERE Reponse = '0' AND ID_admin = '$ID_temporaire_admin' AND ID_acheteur = '$ID_temporaire_acheteur' ;";
			$result1 = mysqli_query($db_handle, $sql1);
			if($data = mysqli_fetch_assoc($result1))
            {
                $message = $data['Message'];
                $objet = $data['Objet'];
            }

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
				<ul class="navbar-nav">       
					<li class="nav-item">
						<a class="nav-link" href="accueil.php">Accueil</a>
					</li>
					<li class="nav-item dropdown">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" id="ades">Mon eBay</button>
					  	<div class="dropdown-menu" id="menu-deroulant">
						    <a class="nav-link dropdown-item" href="vendre.php">Vendre</a>
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
						<i class="nav-link fas fa-shopping-cart" style="margin-top: 5px;"></i>
					</li>    
				</ul>      
			</div> 
		</nav>

		<br><br><br>
		<div class="container features">
			<div class="row"> 
			    <div class="col-lg-9 col-md-9 col-sm-12" style="position: relative; height: 800px;">
			    	<div class="panel border" style="padding: 1em; border-radius: 5px;" id="panel_vendre">
					    <div class="panel-heading">
					    	<br><h2 class="text-center">Contenu du message</h2><br>
					    </div>
					    <div class="panel-body">
							<form method="post" action="" enctype="multipart/form-data">
								<?php 
								echo '<div class="form-group">';
								echo "Objet: ".$objet;
								echo "<br>";
								echo "Message: ".$message;
								echo "<br>";
								echo '</div>';
								//fermer la connexion
								mysqli_close($db_handle); 
								?>
						             	
						    </form>	
						        
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
								<form action="message.php" method="post">
				                    <div class="form-group">
				                    	<input class="form-control" style="width: 30%; margin: 0 auto" name="buttonrepondre" type="submit" value="Repondre">
									</div>
								</form>	

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
				    <div class="panel border" style="display: none; padding: 1em; padding-bottom: 2em; border-radius: 5px;" id="panel_supp_vendeur">
					    <div class="panel-body row">
					    	<div class="col-lg-6 col-md-6 col-sm-12" style="position: relative; min-height: 400px;">
					    		<br><h2 class="text-center">Supprimer une vente</h2><br>
							    <form action="suppression_produit.php" method="post">
									<div class="form-group">
				                        <input class="form-control" style="width: 50%; margin: 0 auto" type="number" name="id" placeholder="ID de l'article" required>
				                    </div>
				                    <div class="form-group">
				                    	<input class="form-control" style="width: 30%; margin: 0 auto" name="buttonsupprimer" type="submit" value="Supprimer">
									</div>
								</form>	
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12" style="position: relative; min-height: 400px;">
								<br><h2 class="text-center">Mes ventes</h2><br>
								<?php
									if(count($ID_item)==0)	
									{?>
										<div class="panel-body row border" style="width: 80%; margin: 0 auto; padding-top: 10px;"><?php 
											echo '<p class = "text-center">Vous ne vendez rien ! Commencez à vendre <a href="vendre.php">ici</a></p>';?>
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
				        </div>
				    </div>
					<div class="panel" style="display: none;" id="panel_mes_vendeur">
					    <div class="panel-heading">
					    	<br><h2 class="text-center">Messages</h2><br>
					    </div>
					    <div class="panel-body">					
				        </div>
				    </div>
				    <div class="panel" style="display: none;" id="panel_o_vendeur">
					    <div class="panel-heading">
					    	<br><h2 class="text-center">Offres</h2><br>
					    </div>
					    <div class="panel-body"> <?php
				    		if(count($ID_i)==0)	
								{?>
									<div class="panel-body border" style="width: 80%; margin: 0 auto; padding-top: 10px;"><?php 
										echo '<p class = "text-center">Vous ne vendez rien ! Commencez à vendre <a href="vendre.php">ici</a></p>';?>
									</div><?php
								}
							else
				            { 
				            	for ($i = 0 ; $i<count($ID_a); $i++)
				            	{
				            		if($stat_offre[$i] == 2)
			            			{
			            				echo "Un acheteur vous a fait une proposition : <br>";?>
										<div class="panel-body row border" style="margin-bottom: 1em; padding: 2px;">
											<div class="col-lg-3 col-md-3 col-sm-12"><?php 
												echo '<img src = "images_web/'.$table_photo_offre["$ID_i[$i]"].'" height = 100 width = 100 >';?>
											</div>
											<div class="col-lg-9 col-md-9 col-sm-12"><?php 
												echo "Item n°".$ID_i[$i]." : ".$table_item_offre["$ID_i[$i]"][1].".<br>";
												echo "Offre proposée : ".$prix_a[$i]."€<br>";
												echo "Votre prix: ".$prix_v[$i]."€<br>";
												echo $tentative[$i]."e tentative. <br>";?>
											</div>
										</div>
										<?php 
												echo '<form action="" method="post">
													<div class="row">
														<div class="col-lg-3 col-md-3 col-sm-12">
															<input class="btn" name="'.$refuser[$i].'" type="submit" value="Refuser la proposition">
														</div>
														<div class="col-lg-3 col-md-3 col-sm-12">
															<input class="btn" name="'.$accepter[$i].'" type="submit" value="Accepter la proposition">
														</div>
														<div class="row col-lg-6 col-md-6 col-sm-12">
															<div class="col-lg-4 col-md-4 col-sm-12">
																<input class="form-control" style="width:120px;" type="text" name="contre_offre" placeholder="Contre-offre">
															</div>
															<div class="col-lg-3 col-md-3 col-sm-12">
																<input class="btn" name="'.$soumettre[$i].'" type="submit" value="Soumettre">
															</div>
															<div class="col-lg-6 col-md-6 col-sm-12"></div>
														</div>
													</div>
												</form>';
									}
								}
							}?>     					
				        </div>
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
					document.getElementById("admin").onclick = function() {return false;}
				</script> <?php
			}

			else
			{?>
				<script type="text/javascript">
					var x = document.getElementById("ad");
					x.style.display = "block";
				</script> <?php
			}?>

		<script type="text/javascript">
			var panel_supp = document.getElementById("panel_supp_vendeur");
			var panel_vendre = document.getElementById("panel_vendre");
			var panel_mes = document.getElementById("panel_mes_vendeur");
			var panel_offres = document.getElementById("panel_o_vendeur");

			document.getElementById("bv1").onclick = function() {
				panel_vendre.style.display ="block";
				panel_supp.style.display ="none";
				panel_mes.style.display ="none";
				panel_offres.style.display ="none";
			}

			document.getElementById("bv2").onclick = function() {
				panel_vendre.style.display ="none";
				panel_supp.style.display ="block";
				panel_mes.style.display ="none";
				panel_offres.style.display ="none";
			}

			document.getElementById("bv3").onclick = function() {
				panel_supp.style.display ="none";
				panel_vendre.style.display ="none";
				panel_mes.style.display ="block";
				panel_offres.style.display ="none";
			}

			document.getElementById("bv4").onclick = function() {
				panel_supp.style.display ="none";
				panel_vendre.style.display ="none";
				panel_mes.style.display ="none";
				panel_offres.style.display ="block";
			}
		</script>
	</body> 
</html> 