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

	//identifier votre BDD
    $database = "ebay ece paris";
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);
	$erreur ="";

	//Pour la partie suppression de ventes
	$table_item = array();
	$table_photo = array();
	$nom_item = array();
	$ID_item = array();

	$filevideo = isset($_POST["filevideo"])? $_POST["filevideo"] : "";
	//Récuperation des ID_item du vendeur (dont admins) connecté
	$sql1 = "SELECT * FROM item WHERE ID_vendeur LIKE '$id' ";
	$r1 = mysqli_query($db_handle, $sql1);

	if (mysqli_num_rows($r1) != 0) 
	{
		$i=0; $j=0;
		$temp = array();
		while ($data = mysqli_fetch_assoc($r1)) 
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
		$sql2 = "SELECT * FROM photo WHERE ID_item LIKE '$ID_item[$a]' ";
		$r2 = mysqli_query($db_handle, $sql2);
		
		if (mysqli_num_rows($r2) != 0) 
		{
			$data = mysqli_fetch_assoc($r2);
			$photo = $data['Nom_photo'];
			$table_photo["$ID_item[$a]"]= $photo; //array de photo dans tableau associatif
		}
	}

	if (isset($_POST["boutonajoutproduit"])) 
	{
	  	$nom = isset($_POST["nom"])? $_POST["nom"] : "";
		$filephoto = isset($_POST["filephoto"])? $_POST["filephoto"] : "";
		
		$description = isset($_POST["description"])? $_POST["description"]: "";
		$categorie = isset($_POST["categorie"])? $_POST["categorie"] : "";
		$prix = isset($_POST["prix"])? $_POST["prix"] : "";
		$vente1 = isset($_POST["vente1"])? $_POST["vente1"] : ""; //achat immediat
		$vente2 = isset($_POST["vente2"])? $_POST["vente2"] : ""; //enchere ou offre
		$datedebut = isset($_POST["datedebut"])? $_POST["datedebut"] : "";
		$heuredebut = isset($_POST["heuredebut"])? $_POST["heuredebut"] : "";
		$datefin = isset($_POST["datefin"])? $_POST["datefin"] : "";
		$heurefin = isset($_POST["heurefin"])? $_POST["heurefin"] : "";
		$prixdepart = isset($_POST["prixdepart"])? $_POST["prixdepart"] : "";

		if ($nom == "")  
			$erreur .= "Nom est vide. <br>"; 
		
		if ($_FILES['filephoto']['name'][0] =="") 
	 		$erreur .= "Aucune photo n'a été ajouté. <br>";
	 	
	 	//verification de type des photos :
		$countfiles = count($_FILES['filephoto']['name']);

		for($i=0;$i<$countfiles;$i++)
		{
			if ($_FILES['filephoto']['type'][$i] != "image/jpeg" && $_FILES['filephoto']['name'][0] !="")
				$erreur .= "Une ou plusieurs images ne sont pas en .jpg. <br>";	
		}

	 	//vérification du type de la vidéo

		if ($description == "")  
		 	$erreur .= "La description est vide. <br>"; 
		
		if ($categorie == "")
		 	$erreur .= "La catégorie est vide. <br>"; 
		 
		if (($prix == "" && $vente1 != "") || ($prix == "" && $vente1 != "" && $vente2 == "offre") || ($prix == "" && $vente2 == "offre")) 
		 	$erreur .= "Le prix est vide. <br>"; 
		
		if ($prix != "" && $vente2 == "enchere" && $prixdepart != "" && $prix<$prixdepart)
		 	$erreur .= "Le prix normal doit être supérieur au prix de départ pour l'enchere. <br>";
		
		if ($vente1 == "" && $vente2 =="")
		 	$erreur .= "Le choix de vente est vide. <br>";
		
		if ($vente2 == "enchere")
		{
		 	if ($datedebut == "")
		 		$erreur .= "Vous n'avez pas choisi une date de début pour l'enchère du produit. <br>";
		 	if ($heuredebut == "")
		 		$erreur .= "Vous n'avez pas choisi une heure de début pour l'enchère du produit. <br>";
		 	if ($datefin == "")
		 		$erreur .= "Vous n'avez pas choisi une date de fin pour l'enchère du produit. <br>";
		 	if ($heurefin == "")
		 		$erreur .= "Vous n'avez pas choisi une heure de fin pour l'enchère du produit. <br>";
		 	if ($datedebut != "" && $datedebut < $date_actuelle)//date de début inférieur à ajd
		 		$erreur .= "La date de début ne doit pas être dans le passé. <br>";
		 	if ($datefin != "" && ($datefin < $date_actuelle || $datefin < $datedebut)) //date de fin inf à ajd ou de déb
		 			$erreur .= "La date de fin ne doit pas être dans le passé. <br>";
		 	//les heures :
		 	if ($datedebut == $date_actuelle && $heuredebut != "" && $heuredebut < $heure_actuelle) //H déb inf à actuelle
		 		$erreur.= "Heure de début ne peut pas être dans le passé.<br>.";
		 	if ($datedebut == $datefin && $heurefin != "" && $heuredebut != "" && $heurefin <= $heuredebut)
		 		$erreur .= "Heure de fin ne peut pas se terminer avant l'heure de début. <br>.";
		}
		
		if ($prixdepart == "" && $vente2 == "enchere") 
	 	 	$erreur .= "Le prix de départ pour l'enchère est vide. <br>";
	 	 
	 	if ($prixdepart >= $prix && $prixdepart != "" && $prix != "" && $vente2 == "enchere")
	 	 	$erreur .= "le prix pour l'enchère doit être inférieur au prix normal du produit. <br>";
	 	
	 	//Si tous les champs sont rempli correctement
		if ($erreur == "") 
		{
		 	$type_vente_choisi = $vente1." ". $vente2;

		    ///BDD
	        if ($db_found) 
	        {
				
				//s'il ne s'agit que d'un enchère Sinon le prix reste en prix par défaut: 
				if (strlen($type_vente_choisi) == 8)
					$prix = $prixdepart;

		        $sql = "INSERT INTO item(ID_vendeur, ID_type_vente, Nom_item, Description, Categorie, Prix, Video) VALUES ($id,'$type_vente_choisi','$nom','$description','$categorie','$prix','$filevideo');";
		        $result = mysqli_query($db_handle, $sql);
		        //Normalement c'est ajouté , mtn vérifions et extraction de l'ID: 
		        $sql3 = "SELECT LAST_INSERT_ID(ID_item) FROM item ";
		        $r3 = mysqli_query($db_handle, $sql3);

	        	$last_id_item = "";
	           	if (mysqli_num_rows($r3) != 0)
	            {
	                echo "Votre item a été ajouté avec succes";
	                while ($data = mysqli_fetch_assoc($r3)) 
                    {
                        $last_id_item = $data['LAST_INSERT_ID(ID_item)'];
                    }
		        } 

		        //Ajout dans la table photo
				$countfiles = count($_FILES['filephoto']['name']);
				for($i=0;$i<$countfiles;$i++)
				{
					$filenamephoto = $_FILES['filephoto']['name'][$i];
					move_uploaded_file($_FILES['filephoto']['tmp_name'][$i],'images_web/'.$filenamephoto);
					$sql = "INSERT INTO photo(Nom_photo, ID_item) VALUES ('$filenamephoto','$last_id_item');";
			        $result = mysqli_query($db_handle, $sql);
				}
				
				//Ajout dans la liste d'enchere si enchere.
				if (strlen($type_vente_choisi) == 8 || strlen($type_vente_choisi) == 22)
				{
					echo "je suis dedans";
					$heuredebut .=":00";
					$heurefin .=":00";
					echo "$last_id_item"."<br>";
					echo "$datedebut"."<br>";
					echo "$heuredebut"."<br>";
					echo "$datefin"."<br>";
					echo "$heurefin"."<br>";
					echo "$prixdepart"."<br>";
					$sql = "INSERT INTO liste_enchere(ID_item, Date_debut, Heure_debut, Date_fin, Heure_fin, Prix_premier, Prix) VALUES ('$last_id_item', '$datedebut', '$heuredebut', '$datefin', '$heurefin', '$prixdepart','$prixdepart');";
					$result = mysqli_query($db_handle, $sql);
				}
	        }
	        else 
	            echo "Database not found";
	    }   
	    else 
	    	echo "Erreur : <br>$erreur";
	}

	//Pour la partie offres
	$contre_offre = isset($_POST["contre_offre"])? $_POST["contre_offre"] : "";
	$sql4 = "SELECT * FROM meilleur_offre WHERE ID_vendeur LIKE '$id'";
	$r4 = mysqli_query($db_handle, $sql4);

	$table_item_offre = array();
	$table_photo_offre = array();
	$refuser = array();
	$accepter = array();
	$soumettre = array();

	$ID_a =array();
	$ID_i = array();
	$prix_a = array();
	$prix_v = array();
	$tentative = array();

	if (mysqli_num_rows($r4) != 0)
	{
		$i = 0;
		while ($data = mysqli_fetch_assoc($r4)) 
		{
			$ID_a[$i] = $data['ID_acheteur'];
			$ID_v[$i] = $data['ID_vendeur'];
			$ID_i[$i] = $data['ID_item'];
			$prix_a[$i] = $data['Prix_acheteur'];
			$prix_v[$i] = $data['Prix_vendeur'];
			$tentative[$i] = $data['Tentative'];
			$stat_offre[$i] = $data['Statut']; // = 1 (acheteur) / = 2 (vendeur) / = 3 (acceptée) / = 4 (perdue)
			$i++;
		}

		for ($u = 0 ; $u < count($ID_i) ; $u++)
		{ 
			//nombre d'item (duplicata comprise)
			$temp = array();
			$sql5 = "SELECT * FROM item WHERE ID_item LIKE '$ID_i[$u]';"; //retrouver les ID_items issu de ID_item[]
			$r5 = mysqli_query($db_handle, $sql5);

			if (mysqli_num_rows($r5) != 0)
			{
				while ($data = mysqli_fetch_assoc($r5)) 
				{
					$i_temp = 0;
					$temp[$i_temp] = $ID_i[$u]; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
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

					$table_item_offre["$ID_i[$u]"] = $temp; // Tableau associatif
				}
			}

			//Récuperation de la première photo de chaque item
			for($a=0; $a < count($ID_i); $a++)
			{
				$sql = "SELECT * FROM photo WHERE ID_item LIKE '$ID_i[$a]' ";
				$r = mysqli_query($db_handle, $sql);
				
				if (mysqli_num_rows($r) != 0) 
				{
					$data = mysqli_fetch_assoc($r);
					$photo = $data['Nom_photo'];
					$table_photo_offre["$ID_i[$a]"]= $photo; //array de photo dans tableau associatif
				}
			}
		}
	}
	for ($a = 0 ; $a < count($ID_i) ; $a++)
	{
		$refuser[$a] = "refuser_".$a;
		$accepter[$a] = "accepter_".$a;
		$soumettre[$a] = "soumettre_".$a;
	}
	for ($a = 0 ; $a < count($ID_i) ; $a++)
	{
		if(isset($_POST["$refuser[$a]"]))
		{
			//A l'acheteur de répondre
			$sql = "UPDATE meilleur_offre SET Statut = '1' WHERE ID_item = $ID_i[$a] AND ID_vendeur = $id AND ID_acheteur = $ID_a[$a];";
			$result = mysqli_query($db_handle, $sql);
		}
	}
	for ($a = 0 ; $a < count($ID_i) ; $a++)
	{
		if(isset($_POST["$accepter[$a]"]))
		{
			//Offre acceptée pour l'acheteur
			$sql7 = "UPDATE meilleur_offre SET Statut = '3' WHERE ID_item = $ID_i[$a] AND ID_vendeur = $id AND ID_acheteur = $ID_a[$a];";
			$r7 = mysqli_query($db_handle, $sql7);
			$sql8 = "UPDATE meilleur_offre SET Statut = '4' WHERE ID_item = $ID_i[$a] AND ID_vendeur = $id AND ID_acheteur != $ID_a[$a]";
			$r8 = mysqli_query($db_handle, $sql8);
		}
	}

	for ($a = 0 ; $a < count($ID_i) ; $a++)
	{
		if(isset($_POST["$soumettre[$a]"]))
		{
			$sql11 = "UPDATE meilleur_offre SET Statut = '6' , Prix_vendeur = $contre_offre WHERE ID_item = $ID_i[$a] AND ID_vendeur = $id AND ID_acheteur = $ID_a[$a];";
			$r11 = mysqli_query($db_handle, $sql11);
		}
	}
	//fermer la connexion
	mysqli_close($db_handle); 
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
				<div class="col-lg-3 col-md-3 col-sm-12" style="position: relative;">
					<h3 class="text-center">eBay ECE</h3>
					<p></p>
					
			        <div class="list-group">
			        	<button type="button" class="list-group-item btn" style="width: 100%;" id="bv1">Vendre</button>
			        	<button type="button" class="list-group-item btn" style="width: 100%;" id="bv2">Supprimer une vente</button>
			        	<button type="button" class="list-group-item btn" style="width: 100%;" id="bv3">Messages</button>
			        	<button type="button" class="list-group-item btn" style="width: 100%;" id="bv4">Offres</button>
			        </div>	
			    </div>
			    <div class="col-lg-9 col-md-9 col-sm-12" style="position: relative; height: 800px;">
			    	<div class="panel border" style="padding: 1em; border-radius: 5px;" id="panel_vendre">
					    <div class="panel-heading">
					    	<br><h2 class="text-center">Vendre</h2><br>
					    </div>
					    <div class="panel-body">
							<form method="post" action="" enctype="multipart/form-data">
						       	<div class="form-group">
						          	<div class="row">
						          		<div class="col-lg-6 col-md-6 col-sm-12">
						           			<p class="font-weight-bold">Nom du produit</p>
						               		<input class="form-control" style="width: 100%" type="text" name="nom" placeholder="Nom du produit" required>
						               	</div>
							            <div class="col-lg-6 col-md-6 col-sm-12">
							                <p class="font-weight-bold">Prix</p>
							                <input class="form-control" type="number" style="width: 100%" name="prix" placeholder="Prix">
							            </div>
							        </div>
						        </div>
						        <div class="form-group">
						          	<div class="row">
						           		<div class="col-lg-6 col-md-6 col-sm-12">
						                    <p class="font-weight-bold">Photo(s)</p>
						                    <input type="file" name="filephoto[]" id="file" multiple required>
						                </div>
										<div class="col-lg-6 col-md-6 col-sm-12">
							                <p class="font-weight-bold">Vidéo (facultative)</p>
						                    <input class="form-control" type="text" style="width: 100%" name="filevideo" placeholder="URL Video">
							            </div>
							        </div>
				                </div>
						        <div class="form-group">
						            <textarea name="description" rows="5" cols="100" placeholder="Description" id="description" required></textarea>
						        </div>
						        <div class="form-group">
						            <p class="font-weight-bold">Catégorie(s)</p>
						            <input type="radio" name="categorie" value="Ferraille_tresor" id="cb">Ferraille ou Trésor
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
						           	<input class="form-control" style="width:200px; margin: 0 auto" name="boutonajoutproduit" type="submit" value="Ajouter le produit">
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
										echo '<p class = "text-center">Aucune offre...</p>';?>
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