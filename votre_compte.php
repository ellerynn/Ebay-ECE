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

	include("modification_donnees.php");

	$database = "ebay ece paris";
	$db_handle = mysqli_connect('localhost', 'root', '');
	$db_found = mysqli_select_db($db_handle, $database);

	//Déclaration de variable général
	$nom = "";
	$prenom = "";
	$email = "";
	$statutNom = "";
	$mdp = "";

	if ($statut == 1){$statutNom = "Administrateur";}
	if ($statut == 2){$statutNom = "Vendeur";}
	if ($statut == 3){$statutNom = "Acheteur";}

	if ($db_found) {
		//récuperation données de la table personne (identique pour tout les statuts)
		$sql = "SELECT * FROM personne WHERE ID = '$id';";
		$result = mysqli_query($db_handle, $sql);
		if (mysqli_num_rows($result) != 0){ // != 0 : il existe une ligne
			while ($data = mysqli_fetch_assoc($result)) //on récupère les données
			{
				$nom = $data['Nom'];
				$prenom = $data['Prenom'];
				$email = $data['Email']; //Même si on connait déjà celui de Adm et Ach, si c'est un vendeur au moins on aura récup son email si utile
				$mdp = $data['Mot_de_passe'];
			}
		}

		//récuperation données de la table vendeur (pas besoin de vérifier si c'est un vendeur, si c'est pas un vendeur : 0 ligne trouvé)
		$sql = "SELECT * FROM vendeur WHERE ID = '$id';";
		$result = mysqli_query($db_handle, $sql);
		if (mysqli_num_rows($result) != 0){ // != 0 : il existe une ligne
			while ($data = mysqli_fetch_assoc($result)) //on récupère les données
			{ 	
				$pseudo = $data['Pseudo'];
				$photo_vendeur = $data['ID_photo'];
				$fond_vendeur = $data['ID_image_fond'];
			}
		}

		//Déclaration de varaibles spécial vendeur
		$pseudo = "Vide";
		$photo_vendeur = "Vide";
		$fond_vendeur = "Vide";

		$sql = "SELECT * FROM vendeur WHERE ID = '$id';";
		$result = mysqli_query($db_handle, $sql);
		if (mysqli_num_rows($result) != 0){ // != 0 : il existe une ligne
			while ($data = mysqli_fetch_assoc($result)) //on récupère les données
			{ 	
				$pseudo = $data['Pseudo'];
				$photo_vendeur = $data['ID_photo'];
				$fond_vendeur = $data['ID_image_fond'];
			}
		}

		//Déclaration de variables spécials acheteur 
		$adresse_ligne1 = "Vide";
		$adresse_ligne2 = "Vide";
		$ville = "Vide";
		$code_postal = "Vide";
		$pays = "Vide";
		$telephone = "Vide";
		$type_carte = "Vide";
		$numero_carte = "Vide";
		$nom_carte = "Vide";
		$date_exp_carte = "Vide";
		$code_securite = "Vide";
		$solde = "Vide";

		$sql = "SELECT * FROM acheteur WHERE ID = '$id';";
		$result = mysqli_query($db_handle, $sql);
		if (mysqli_num_rows($result) != 0){ // != 0 : il existe une ligne
			while ($data = mysqli_fetch_assoc($result)) //on récupère les données
			{ 	
				$adresse_ligne1 = $data['Adresse_ligne1'];
				$adresse_ligne2 = $data['Adresse_ligne2'];
				$ville = $data['Ville'];
				$code_postal = $data['Code_postal'];
				$pays = $data['Pays'];
				$telephone = $data['Telephone'];
				$type_carte = $data['Type_carte'];
				$numero_carte = $data['Numero_carte'];
				$nom_carte = $data['Nom_carte'];
				$date_exp_carte = $data['Date_exp_carte'];
				$code_securite = $data['Code_securite'];
				$solde = $data['Solde'];
			}
		}
	}//End 
	else{
		echo "Database not found";
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
				<!--- Afficher donnée : --->
				<?php
					echo   '<div>
							<h3>Information Générale</h3>
							<table>
									<tr>
										<td> Statut : </td>
										<td> '.$statutNom.'</td>
									</tr>
									<tr>
										<td>Nom : </td>
										<td>'.$nom.'</td>
									</tr>
									<tr>
										<td>Prenom : </td>
										<td>'.$prenom.'</td>
									</tr>
									<tr>
										<td>Email : </td>
										<td>'.$email.'</td>
									</tr>
									<tr>
										<td>Mot de passe : </td>
										<td>'.$mdp.'</td>
									</tr>
									<tr>
										<td></td>
										<td><input type="checkbox" name="general" value="Modifier" style="margin-right: 5px;margin-left: 10px;" id="general" onclick="montrer()">Modifier mes informations générales</td>
									</tr>
							</table>

									<form method = "post" action = "">
										<div class="form-group" style="display: none;" id="infoGeneral">
										<input class="form-control" style="width: 100%" type="text" name="nomGen" placeholder="Nom">
										<input class="form-control" style="width: 100%" type="text" name="prenomGen" placeholder="Prenom">
										<input class="form-control" style="width: 100%" type="text" name="emailGen" placeholder="Email">
										<input class="form-control" style="width: 100%" type="password" name="mdpGen" placeholder="Mot de passe">
										<input class="form-control" style="width:200px; margin: 0 auto" name="modifierGen" type="submit" value="Valider les modifications">
									</form>
							</div>

		<script>
			function montrer() 
			{
				// Get the checkbox
				var checkBox = document.getElementById("general");
				// Get the output text
				var text = document.getElementById("infoGeneral");

				// If the checkbox is checked, display the output text
				if (checkBox.checked == true)
					text.style.display = "block";
				else 
					text.style.display = "none";
			}
		</script>';

					//Cas vendeur:
					if ($statut == 2){
						echo 	'<div>
									<h4>Information Vendeur</h4>
									<img src = "images_web/'.$photo_vendeur.'" height = "200" width = "200">
									<p>Pseudo :'.$pseudo.'</p>
									<p>Je ne sais pas où mettre cette image dans le css en background avec php dans je l affiche là pour l instant</p>
									<img src = "images_web/'.$fond_vendeur.'">
								</div>
								
								<input type="checkbox" name="vendeurI" value="Modifier" style="margin-right: 5px;margin-left: 10px;" id="genVendeur" onclick="montrerV()">Modifier mes informations vendeurs
								<form method="post" action="" enctype="multipart/form-data">
									<div class="form-group" style="display: none;" id="infoVendeur">
									<table><tr><td>
									Modifier votre photo de profil:</td>
									<td><input type="file" name="filephoto[]" id="file" multiple></td>
									<td><input class="form-control" style="width:200px; margin: 0 auto" name="buttonmodifierphotoprofil" type="submit" value="Valider les modifications"></td></tr>
									<tr><td>Modifier votre image de fond:</td>
									<td><input type="file" name="filephotofond[]" id="file" multiple>
									<td><input class="form-control" style="width:200px; margin: 0 auto" name="buttonmodifierimagefond" type="submit" value="Valider les modifications"></td></tr></table>
								</form>

	<script>
		function montrerV() 
		{
			// Get the checkbox
			var checkBox = document.getElementById("genVendeur");
			// Get the output text
			var text = document.getElementById("infoVendeur");

			// If the checkbox is checked, display the output text
			if (checkBox.checked == true)
				text.style.display = "block";
			else 
				text.style.display = "none";
		}
	</script>';
						}

						//Cas acheteur
						if($statut == 3){
							echo '<div>
									<h4>Information Acheteur</h4>
										<div>
											<h5>Les coordonnées de livraison</h5>
											<table>
												<tr>
													<td>Adresse ligne 1 : </td>
													<td>'.$adresse_ligne1.'</td>
												</tr>
												<tr>
													<td>Adresse ligne 2 : </td>
													<td>'.$adresse_ligne2.'</td>
												</tr>
												<tr>
													<td>Ville : </td>
													<td>'.$ville.'</td>
												</tr>
												<tr>
													<td>Code postal</td>
													<td>'.$code_postal.'</td>
												</tr>
												<tr>		
													<td>Pays : </td>
													<td>'.$pays.'</td>
												</tr>
												<tr>
													<td>Téléphone : </td>
													<td>'.$telephone.'</td>
												</tr>
												<tr>
													<td></td>
													<td><input type="checkbox" name="infoAcheteurCoords" value="Modifier" style="margin-right: 5px;margin-left: 10px;" id="genAchC" onclick="montrerAC()">Modifier mes informations générales</td>
												</tr>
											</table>
										</div>

										<form method = "post" action = "">
											<div class="form-group" style="display: none;" id="infoAcheteurCoords">
												<input class="form-control" style="width: 100%" type="text" name="adresseUn" placeholder="Adresse ligne 1" required>
												<input class="form-control" style="width: 100%" type="text" name="adresseDeux" placeholder="Adresse ligne 2" required>
												<input class="form-control" style="width: 100%" type="text" name="ville" placeholder="Ville" required>
												<input class="form-control" style="width: 100%" type="number" name="codePostal" placeholder="Code postal" required>
												<input class="form-control" style="width: 100%" type="text" name="pays" placeholder="Pays" required>
												<input class="form-control" style="width: 100%" type="number" name="telephone" placeholder="Téléphone" required>

												<input class="form-control" style="width:200px; margin: 0 auto" name="bontonaddcoords" type="submit" value="Valider les modifications" required>
											</div>
									</form>
	<script>
		function montrerAC() 
		{
			// Get the checkbox
			var checkBox = document.getElementById("genAchC");
			// Get the output text
			var text = document.getElementById("infoAcheteurCoords");

			// If the checkbox is checked, display the output text
			if (checkBox.checked == true)
				text.style.display = "block";
			else 
				text.style.display = "none";
		}
	</script>';

								echo    '<div>
											<h5>Les coordonnées de bancaire</h5>
												<table>
													<tr>
														<td>Type de votre carte : </td>
														<td>'.$type_carte.'</td>
													</tr>
													<tr>
														<td>Numero de votre carte : </td>
														<td>'.$numero_carte.'</td>
													</tr>
													<tr>
														<td>Nom du titulaire : </td>
														<td>'.$nom_carte.'</td>
													</tr>
													<tr>';
												echo    "<td>Date d'expiration : </td>";
												echo 	'<td>'.$date_exp_carte.'</td>
													</tr>
													<tr>
														<td>Code de sécurité :</td>
														<td>'.$code_securite.'</td>
													</tr>
													<tr>
														<td>Le solde : </td>
														<td>'.$solde.'</td>
													</tr>
													<tr>
														<td></td>
														<td><input type="checkbox" name="infoAcheteurCarte" value="Modifier" style="margin-right: 5px;margin-left: 10px;" id="genAchCarte" onclick="montrerACa()">Modifier mes informations générales</td>
													</tr>
												</table>
										</div> 
										<form method = "post" action = "">
											<div class="form-group" style="display: none;" id="infoAcheteurCarte">
												<p>Type de carte:</p>
												<select  name = "typecarte">
													<option value ="VISA">VISA</option>
													<option value ="MASTERCARD">MASTERCARD</option>
													<option value ="AMERICAN EXPRESS">AMERICAN EXPRESS</option>
												</select>
												<input class="form-control" style="width: 100%" type="number" name="numero_carte" placeholder="Numéro de la carte" required>
												<input class="form-control" style="width: 100%" type="text" name="titulaire_carte" placeholder="Titulaire" required>
												<input class="form-control" style="width: 100%" type="date" name="date_exp_carte" placeholder="expiration" required>
												<input class="form-control" style="width: 100%" type="password" name="mdpasse" placeholder="Code de sécurité" required>
													<input class="form-control" style="width:200px; margin: 0 auto" name="boutonajoutcarte" type="submit" value="Valider les modifications" required>
											</div>
										</form>
								</div>
	<script>
		function montrerACa() 
		{
			// Get the checkbox
			var checkBox = document.getElementById("genAchCarte");
			// Get the output text
			var text = document.getElementById("infoAcheteurCarte");
			// If the checkbox is checked, display the output text
			if (checkBox.checked == true)
				text.style.display = "block";
			else 
				text.style.display = "none";
		}
	</script>';

						}
					
				?>

				<!--- Afficher donnée : --->

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