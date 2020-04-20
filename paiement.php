<?php
	include("const.php");
	// On prolonge la session
	session_start();
	// On teste si la variable de session existe et contient une valeur
	if(isset($_SESSION['prix_total']))
	{
		$prixTot = $_SESSION["prix_total"];
		$login = $_SESSION['login'];
		$psw = $_SESSION['psw'];
		$statut = $_SESSION['Statut'];
		$id = $_SESSION['ID'];
	}
	else
	{
	  // Si inexistante ou nulle, on redirige vers le panier
	  header('Location: panier.php');
	  exit();
	}
	include ("modification_donnees.php");
	$database = "ebay ece paris";
	$db_handle = mysqli_connect('localhost', 'root', '');
	$db_found = mysqli_select_db($db_handle, $database);

	//traitement des données de la date actuelle
		date_default_timezone_set('Europe/Paris');
		$today = getdate();
		$date_actuelle = "";
		if (strlen($today["mon"]) != 2) //nombre en mois
			$date_actuelle .=  $today["year"]."-0".$today["mon"]."-";
		else
			$date_actuelle .=  $today["year"]."-".$today["mon"]."-";
		if(strlen($today["mday"]) !=2) //nombre en jour
			$date_actuelle .= "0".$today["mday"];
		else
			$date_actuelle .= $today["mday"];

		$heure_actuelle = "";
		if (strlen($today["hours"]) != 2) //nombre H
			$heure_actuelle .=  "0".$today["hours"].":";
		else
			$heure_actuelle .= $today["hours"].":";

		if(strlen($today["minutes"]) !=2) //nombre M
			$heure_actuelle .= "0".$today["minutes"].":";
		else
			$heure_actuelle .= $today["minutes"].":";
		if(strlen($today["seconds"]) !=2) //nombre S
			$heure_actuelle .= "0".$today["seconds"];
		else
			$heure_actuelle .= $today["seconds"];
		//FIN traitement des données de la date actuelle

	if ($db_handle)
	{
		//Coordonnées et carte
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

		$erreurCoords = "";
		$infosCoords = "";
		//Coordonnées de livraison:
		if ($adresse_ligne1 == ""){
			$erreurCoords .= "Veuillez saisir les coordonnées de livraison. <br>";
			$infosCoords = "false";
		}else
		{
			$infosCoords = "true";
		}

		$erreurCarte = "";
		$infosCarte = "";
		if ($type_carte == ""){
			$erreurCarte .= "Veuillez saisir les coordonnées bancaires. <br>";
			$infosCarte = "false";
		}else
		{
			$infosCarte = "true";
		}

		$erreurPaiement = "";
		if (isset($_POST["payer"])){
			$montant = "";
			$expi = "";
	  		$code_saisie = isset($_POST["code"])? $_POST["code"] : "";
			$sql = "SELECT * From acheteur WHERE Code_securite = $code_saisie AND ID = $id";
			$result = mysqli_query($db_handle, $sql);
			if (mysqli_num_rows($result) != 0) 
			{
				while ($data = mysqli_fetch_assoc($result)) 
				{
					$montant = $data['Solde'];
					$expi = $data['Date_exp_carte'];
				}
				if ($expi >= $date_actuelle)
				{
					if ($montant >= $prixTot) //suffisant
					{
						//MODIFIER
						$restant = $solde - $prixTot;
						$sql = "UPDATE acheteur SET Solde = $restant WHERE ID = $id";
						$result = mysqli_query($db_handle, $sql);
						echo "<br><br><br>Payement réussi";
						//videment du panier de ceux payer
						//Achat immédiat
						$sql = "SELECT * FROM panier WHERE ID = '$id' AND ID_type_vente = 'achat_immediat'";
						$result = mysqli_query($db_handle, $sql);
						if (mysqli_num_rows($result) != 0) 
						{
							$temp_item = array();
							$i = 0;
							while ($data = mysqli_fetch_assoc($result)) 
							{
								$temp_item["$i"] = $data['ID_item'];
								$i++;
							}
							for ($i = 0; $i<count($temp_item);$i++){// ON SUPPRIME TOUUUUUUUUT TOUUUUUT Acheter = Détruire
								$sql = "DELETE FROM item WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);

								$sql2 = "DELETE FROM photo WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql2);
										
								$sql = "DELETE FROM liste_enchere WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);

								$sql = "DELETE FROM encherir WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);

								$sql = "DELETE FROM meilleur_offre WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);

								$sql = "DELETE FROM panier WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);
							}
						}
						//Offre terminé 
						$sql = "SELECT * FROM meilleur_offre WHERE ID_acheteur = '$id' AND Statut = 3";
						$result = mysqli_query($db_handle, $sql);
						if (mysqli_num_rows($result) != 0) 
						{
							$temp_item = array();
							$i=0;
							while ($data = mysqli_fetch_assoc($result)) 
							{
								$temp_item["$i"] = $data['ID_item'];
								$i++;
							}
							for ($i = 0; $i<count($temp_item);$i++)
							{// ON SUPPRIME TOUUUUUUUUT TOUUUUUT Acheter = Détruire, TOUT les items offres, statut 3 pour lui
								$sql = "DELETE FROM item WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);

								$sql2 = "DELETE FROM photo WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql2);
										
								$sql = "DELETE FROM liste_enchere WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);

								$sql = "DELETE FROM encherir WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);

								$sql = "DELETE FROM meilleur_offre WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);

								$sql = "DELETE FROM panier WHERE ID_item = $temp_item[$i]";
								$result = mysqli_query($db_handle, $sql);
							}
						}
						//Enchère terminé 
						$sql = "SELECT * FROM liste_enchere WHERE Fin = 1"; // Parmi la liste d'enchere Terminer (=1)
						$result = mysqli_query($db_handle, $sql);
						if (mysqli_num_rows($result) != 0) 
						{
							$temp_prix = array();
							$temp_item = array();
							$i=0;
							while ($data = mysqli_fetch_assoc($result))
							{
								$temp_prix[$i] = $data['Prix_premier'];
								$temp_item[$i] = $data['ID_item'];
								$i++;
							}
							for ($i = 0 ; $i < count($temp_prix) ; $i++) // on recupère les prix des gagnants
							{//On cherche si ce prix correspond à une enchère fait pour ce acheteur
								$sql = "SELECT * from encherir WHERE ID_acheteur = $id AND ID_item = $temp_item[$i] AND Prix_acheteur = $temp_prix[$i]";
								$result = mysqli_query($db_handle, $sql);
								if (mysqli_num_rows($result) != 0) // Si un item a été gagné par lui
								{// DELETE TOUUUUUUUT en relation avec ce item car buy
									$sql = "DELETE FROM item WHERE ID_item = $temp_item[$i]";
									$result = mysqli_query($db_handle, $sql);

									$sql2 = "DELETE FROM photo WHERE ID_item = $temp_item[$i]";
									$result = mysqli_query($db_handle, $sql2);
											
									$sql = "DELETE FROM liste_enchere WHERE ID_item = $temp_item[$i]";
									$result = mysqli_query($db_handle, $sql);

									$sql = "DELETE FROM encherir WHERE ID_item = $temp_item[$i]";
									$result = mysqli_query($db_handle, $sql);

									$sql = "DELETE FROM meilleur_offre WHERE ID_item = $temp_item[$i]";
									$result = mysqli_query($db_handle, $sql);

									$sql = "DELETE FROM panier WHERE ID_item = $temp_item[$i]";
									$result = mysqli_query($db_handle, $sql);
								}
							}
						}
						//Tout est bon redirection dans panier
						echo "<script type='text/javascript'>document.location.replace('panier.php');</script>";
					} //solde >= PrixTot
					else
						$erreurPaiement .= "Solde insuffissant, Paiement refusé.<br>";
				}//expiration
				else
					$erreurPaiement .= "Carte expiré.<br>";

			}// Si code de sécurité bon
			else //Pas encore afficher
				$erreurPaiement .= "Code invalide.<br>";

		}//Si bouton payer

		$erreurDonnees = "true";
		if (isset($_POST["boutonPaiement"]))
		{	
			if ($adresse_ligne1 != "" && $type_carte != "")
			{
				$erreurDonnees = "false";
			}
			else
			{
				$erreurPaiement .= "Des champs sont vides";
				$erreurDonnees = "true";
			}
		}
	}
	else
		echo "BDD non retrouvé";

	
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
						<a class="nav-link" href="panier.php"><i class="fas fa-shopping-cart"></i></a>
					</li>    
				</ul>      
			</div> 
		</nav>	
<!--atchom-->
<br><br><br>
<input type="hidden" id="variableAPasser" value="<?php echo $erreurDonnees;?>"/>
<?php
echo '
<div id = "afficherfalse" style="display: block;">
	<div class ="form-group" id ="infosCoords'.$infosCoords.'" style="display: none;">
		<h5>Vos coordonnées de livraison</h5>
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
		</table>
		<input class="form-control btn border btn-outline-secondary rounded-lg" name="bontonaddcoords" id="afficheFormCoords" type="submit" value="Modifier les coordonnées de livraison">
	</div>';
	if ($erreurCoords != "")
	{
		echo $erreurCoords;
	}
?>
	<form method = "post" action = "">
		<?php

  echo '<div class="form-group" style="display: none;" id="formulaireCoords'.$infosCoords.'">';
	?>
			<input class="form-control" style="width: 100%" type="text" name="adresseUn" placeholder="Adresse ligne 1" required>
			<input class="form-control" style="width: 100%" type="text" name="adresseDeux" placeholder="Adresse ligne 2">
			<input class="form-control" style="width: 100%" type="text" name="ville" placeholder="Ville" required>
			<input class="form-control" style="width: 100%" type="number" name="codePostal" placeholder="Code postal" required>
			<input class="form-control" style="width: 100%" type="text" name="pays" placeholder="Pays" required>
			<input class="form-control" style="width: 100%" type="text" pattern="\d+" minlength="10" maxlength="10" name="telephone" placeholder="Téléphone" required>

			<input class="form-control" style="width:200px; margin: 0 auto" name="bontonaddcoords" type="submit" value="Valider les modifications" required>
		</div>
	</form>
	<?php
echo'<div class ="form-group" id ="infosCarte'.$infosCarte.'" style="display: none;">
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
			</table>
			<input class="form-control btn border btn-outline-secondary rounded-lg" name="bontonaddcoords" id="afficheFormCarte" type="submit" value="Modifier les coordonnées bancaire">
	</div>';
	if ($erreurCarte != "")
	{
		echo $erreurCarte;
	}
	?>

	<form method = "post" action = "">
		<?php
  		echo '<div class="form-group" style="display: none;" id="formulaireCarte'.$infosCarte.'">';
		?>
			<p>Type de carte:</p>
			<select  name = "typecarte">
				<option value ="VISA">VISA</option>
				<option value ="MASTERCARD">MASTERCARD</option>
				<option value ="AMERICAN EXPRESS">AMERICAN EXPRESS</option>
			</select>
			<input class="form-control" style="width: 100%" type="text" pattern="\d+" minlength="8" maxlength="19" name="numero_carte" placeholder="Numéro de la carte" required>
			<input class="form-control" style="width: 100%" type="text" name="titulaire_carte" placeholder="Titulaire" required>
			<input class="form-control" style="width: 100%" type="date" name="date_exp_carte" placeholder="expiration" required>
			<input class="form-control" style="width: 100%" type="password" name="mdpasse" minlength = "4" maxlength = "4" pattern = "\d+"placeholder="Code de sécurité" required>
				<input class="form-control" style="width:200px; margin: 0 auto" name="boutonajoutcarte" type="submit" value="Valider les modifications" required>
		</div>
	</form>
	<form method = "post" action = "">
	<input class="form-control" style="width:200px; margin: 0 auto" name="boutonPaiement" id="passerPaiement"type="submit" value="Payer" >
	</form>
	<?php ///Faire ça quand des données ne sont pas saisie
		if ($erreurPaiement != "")
		{
			echo $erreurPaiement;
		}
	?>
</div>
<?php
echo '
<div id = "paiementfalse" style="display: none;">';
echo "<br><br><br><br><br>Prix total à payer : ".$prixTot;
echo'
<form method = "post" action = "">
<input class="form-control" style="width: 100%" type="password" minlength = "4" maxlength = "4" pattern = "\d+" name="code" placeholder="Code de sécurité" required>
<input class="form-control" style="width:200px; margin: 0 auto" name="payer" type="submit" value="Valider" required>
';	
if (isset($_POST["payer"])) 
{
	$database = "ebay ece paris";
	$db_handle = mysqli_connect('localhost', 'root', '');
	$db_found = mysqli_select_db($db_handle, $database);
	if($erreurPaiement=="")
	{
			$sql = "INSERT INTO contact(ID_admin, ID_acheteur, Message) VALUES ('0','$id','$prixTot');";
   			 $result = mysqli_query($db_handle, $sql);
	}

}
echo '</form>';
?>

</div>
<!--atchom-->
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
						<a href="achat.php"><h5 class="text-uppercase font-weight-bold">Achat</h5></a>
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
								<h5 class="text-uppercase font-weight-bold">Vendre</h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold"> <a href="votre_compte.php">Votre compte</a> </h5>
							</li>    
							<li>
								<h5 class="text-uppercase font-weight-bold">Admin</h5>
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
			var variableRecuperee = document.getElementById("variableAPasser").value;
			var a = document.getElementById("afficherfalse");
			var b = document.getElementById("paiementfalse");

			if (variableRecuperee == "false")
			{
				a.style.display = "none";
				b.style.display = "block";
			}
			//Jquery
			if($('#formulaireCoordsfalse').css('display') === 'none')
			{
			    $('#formulaireCoordsfalse').css('display','block');
			}
			if($('#infosCoordstrue').css('display') === 'none')
			{
			    $('#infosCoordstrue').css('display','block');
			}
			if($('#formulaireCartefalse').css('display') === 'none')
			{
			    $('#formulaireCartefalse').css('display','block');
			}
			if($('#infosCartetrue').css('display') === 'none')
			{
			    $('#infosCartetrue').css('display','block');
			}
			if($('#afficherfalse').css('display') === 'block')
			{
			    $('#paiementfalse').css('display','none');
			}
			$(document).ready(function(){
               $("#afficheFormCoords").click(function(){
               		if($('#formulaireCoordstrue').css('display') === 'none')
						$('#formulaireCoordstrue').css('display','block');
				});
                $("#afficheFormCarte").click(function(){
               		if($('#formulaireCartetrue').css('display') === 'none')
						$('#formulaireCartetrue').css('display','block');
				});
            });

		</script>
	</body> 
</html> 